<?php

class VisibilityManager extends BaseModelManager
{
    private static $instance = null;
    private $queries;
    private $cache;
    public static $useVisibility = false;
    private $visibility_user_param_name = ':visibility_user_id';

    private function __construct() {
        $cclass = Yii::app()->params['cache_config']['class'];

        $this->cache = new $cclass();

        if($cclass=='CMemCache'){
            $this->cache->setServers(Yii::app()->params['cache_config']['servers']);
            $this->cache->init();
        }

        //$this->cache->set(Yii::app()->params['cache_keys']['query'], array()); // ezzel ki lehet törölni a cache-t

        $queries = $this->cache->get(Yii::app()->params['cache_keys']['query']);
        $this->queries = (!$queries)?array():$queries;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new VisibilityManager();
        }
        return self::$instance;
    }

    /**
     *
     * Kiegészíti a command-ot a lűthatóságra szűréssel, ha szükséges
     * @param CDbCommand $command
     */
    public function addVisibilityCondition(CDbCommand $command) {
        // ha láthatóságra szűrés ki van kapcsolva, vagy a belépett felhasználó superadmin, akkor nem csinálunk semmit
        if(!self::$useVisibility) return;
        if (Yii::app()->user->getDbUser() && Yii::app()->user->getDbUser()->is_super_admin) return;

        $new_query = $this->getNewQuery($command->getText());

        // null-lal tér vissza, ha nem kellett kiegészíteni a query-t
        if(!is_null($new_query)) {
            $command->setText($new_query);
            $command->bindParamsToPDO();
            $command->bindValuesToPDO();
        }
    }

    /**
     *
     * Cache-be teszi a queryt az adott kulcsra
     * @param string $hash a cache kulcs
     * @param string $query  a query string, ami nem tartalmaz user azonosítót
     */
    public function cacheQuery($hash, $query){
        $this->queries[$hash] = $query;
    }

    /**
     *
     * Az adott kulcs alapján megnézi, hogy a query benne van-e a cache-ben, ha nincs nullal tér vissta
     * @param string $hash
     */
    public function getQueryFromCache($hash){
        return (isset($this->queries[$hash]))?$this->queries[$hash]:null;
    }

    public function getNewQuery($original_query_text) {

        $hash = md5($original_query_text);
        $query = $this->getQueryFromCache($hash);

        // ha megtaláltuk a cache-ben, akkor csak be kell helyetesíteni az aktuális usert
        if(!is_null($query)) {

            return $this->bindSessionUser($query);

        // ha nem, akkor parse-oljuk a query-t és eldöntjük, hogy ki kell-e egészíteni
        } else {
            $parser = new PHPSQLParser();
            $parsed = $parser->parse($original_query_text);
            $new_from = array();
            $exists_table = false;

            if(isset(Yii::app()->user->id) && isset($parsed['SELECT']) && isset($parsed['FROM'])){

                $vis_table_names = array_map(function($table) {return $table.'_visibility';}, Yii::app()->params['visibility_tables']);

                foreach ($parsed['FROM'] as $table) {
                    $table_ = trim($table['table'],'`');
                    if(in_array($table_, $vis_table_names)){
                        // megtaláltuk a visibility táblák valamelyikét,
                        // tehát ez a query már egyszer lett láthatósággal bővítve, de mégsincs benne a cache-be, ezért betesszük
                        // és behelyettesítjük a megfelelő user_id-t
                        $this->cacheQuery($hash, $original_query_text);

                        return $this->bindSessionUser($original_query_text);
                    }
                }

                $table_count = 0;
                foreach ($parsed['FROM'] as $table) {
                    $new_from[] = $table;
                    $table_ = trim($table['table'],'`');

                    if(in_array($table_, Yii::app()->params['visibility_tables'])){
                        $alias = (isset($table['alias']['name']))?$table['alias']['name']:null;
                        $exists_table = true;

                        $visibilitiy = $this->getVisibilityJoinWhereArray($table_, $alias, $table_count);
                        $new_from[] =  $visibilitiy['FROM'];

                        if(!isset($parsed['WHERE'])) $parsed['WHERE'] = array();
                        if(!empty($parsed['WHERE'])) $parsed['WHERE'][] = array('expr_type' => 'operator', 'base_expr' => 'AND', 'sub_tree' => null);
                        $parsed['WHERE'][] = $visibilitiy['WHERE'];

                        $table_count++;
                    }
                }

                if($exists_table){
                    $parsed['FROM'] = $new_from;

                    if(isset($parsed['ORDER'])){
                        foreach ($parsed['ORDER'] AS &$od){
                            if($od['type']!='expression'){
                                $od['type'] = 'expression';
                            }
                        }
                    }

                    $creator = new PHPSQLCreator($parsed);

                    $new_query_text = $creator->created;
                    $this->cacheQuery($hash, $new_query_text);

                    return $this->bindSessionUser($new_query_text);
                }
            }
        }

        return null;
    }

    private function getVisibilityJoinWhereArray($table, $alias, $table_count){
        $result = array();

        $visibility_table_as = "vt_{$table_count}";
        $visibility_table = $table."_visibility";

        $visibility_col_user = "visibility_user_id";
        $visibility_col_name = "visibility_".$table."_id";

        $user_id = Yii::app()->user->id;

        if(is_null($alias)){
            $sql = "SELECT * FROM {$table} LEFT JOIN $visibility_table AS $visibility_table_as ON {$table}.id = {$visibility_table_as}.{$visibility_col_name} AND {$visibility_table_as}.{$visibility_col_user}=".$this->visibility_user_param_name." WHERE ({$visibility_table_as}.{$visibility_col_user} IS NULL)";
        }
        else{
            $sql = "SELECT * FROM {$table} AS {$alias} LEFT JOIN $visibility_table AS $visibility_table_as ON {$alias}.id = {$visibility_table_as}.{$visibility_col_name} AND {$visibility_table_as}.{$visibility_col_user}=".$this->visibility_user_param_name." WHERE ({$visibility_table_as}.{$visibility_col_user} IS NULL)";
        }

        $parser = new PHPSQLParser();
        $parsed = $parser->parse($sql);

        $result['FROM'] = $parsed['FROM'][1];
        $result['WHERE'] = $parsed['WHERE'][0];

        return $result;
    }

    private function bindSessionUser($query_text) {
        return str_replace($this->visibility_user_param_name, Yii::app()->user->id, $query_text);
    }

    public function __destruct(){
        $this->cache->set(Yii::app()->params['cache_keys']['query'], $this->queries);
    }
}