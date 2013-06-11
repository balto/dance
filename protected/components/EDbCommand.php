<?php

Class EDBCommand extends CDbCommand
{
    protected $bulk_insertions = array();
    protected $bulk_insert_count = array();
    protected $bulk_deletions = array();
    protected $bulk_insert_memory_usage = 0;

    protected $bound_values = array();
    protected $bound_params = array();


    const AUTO_EXECUTE_LIMIT = 10; // 10M

	/**
	 * Tömeges insert-hez adatgyűjtés
	 * Több táblába is gyűjthető insert, de minden táblához ugyanazokat a mezőknek kell rendelni
	 *
	 * @param string $table
	 * @param array $columns a lementendő adatok oszlop névre kulcsolva
	 * @throws Exception   ha az adott táblához begyűjtött mezőlista eltér a most beküldöttől
	 */
    public function bulkInsertCollect($table, $columns, $auto_execute = false)
	{
		$memory_start = memory_get_usage();
        $params = array();
		$names = array();
		if (!isset($this->bulk_insert_count[$table])) $this->bulk_insert_count[$table] = 0;
		$this->bulk_insert_count[$table]++;

		foreach($columns as $name=>$value) {
			$names[]=$this->getConnection()->quoteColumnName($name);
            $params[':'.$name.$this->bulk_insert_count[$table]]=$value;
		}

		if (!isset($this->bulk_insertions[$table]['names'])) {
            $this->bulk_insertions[$table]['names'] = $names;
            $this->bulk_insertions[$table]['params'] = array();
            $this->bulk_insertions[$table]['values'] = array();
		} else {
            $diff = array_diff($this->bulk_insertions[$table]['names'], $names);
            if (!empty($diff)) {
                throw new Exception('bulkInsert header mismatch!');
            }
		}

		$this->bulk_insertions[$table]['params'] += $params;
        $this->bulk_insertions[$table]['values'][] = '(' . implode(', ', array_keys($params)) . ')';

        $this->bulk_insert_memory_usage += memory_get_usage() - $memory_start;

        if ($auto_execute && $this->bulk_insert_memory_usage > self::AUTO_EXECUTE_LIMIT * 1024 * 1024) {
            $this->bulkInsertExecute($table);
        }
	}

	/**
	 * Megmondja, hogy várakoznak-e adatok lementésre
	 *
     * @param string $table
     */
	public function hasBulkInsertData($table)
	{
	    return !empty($this->bulk_insertions[$table]);
	}

	/**
	 * Tömeges insert-hez begyűjtött adatokat 1 SQL paranccsal egyben beszúrja
	 *
	 * @param string $table a tábla neve, amelyhez gyűjtött insert adatokat be kell szúrni
	 */
	public function bulkInsertExecute($table, $duplication = '')
	{
	    $max_record_count = Yii::app()->params['bulk_insert_max_record_count'];

	    $result = true;

	    if (!isset($this->bulk_insertions[$table])) {
	        throw new Exception('bulkInsertExecute: insertion data for '.$table.' not found!');
	    }

        $column_names = $this->bulk_insertions[$table]['names'];
    	$params = $this->bulk_insertions[$table]['params'];
        $values = $this->bulk_insertions[$table]['values'];
        $chunks = ceil(count($values)/$max_record_count);
        $params_per_row = (int)(count($params) / count($values));

        for($i=0; $i<$chunks; $i++) {
            $value_chunk = array_slice($values, $max_record_count*$i, $max_record_count);
            $param_chunk = array_slice($params, $max_record_count*$params_per_row*$i, $max_record_count*$params_per_row);

            $sql='INSERT INTO ' . $this->getConnection()->quoteTableName($table)
                . ' (' . implode(', ',$column_names) . ') VALUES '
                . implode(', ', $value_chunk)
                . ' ' . $duplication;

            $result &= (bool)$this->setText($sql)->execute($param_chunk);
        }

        $this->bulkInsertClear($table);
    	return $result;
	}

	/**
	 * Az összes begyűjtött tömeges insert adatot sorban beszúrja
	 * Annyi SQL parancsot hajt végre, ahány táblába gyűjtöttünk előtte adatot
	 *
	 */
	public function bulkInsertExecuteAll()
	{
	    foreach ($this->bulk_insertions as $table => $dummy) {
	        $this->bulkInsertExecute($table);
	    }
	}

	public function bulkInsertClear($table)
	{
	    unset($this->bulk_insertions[$table]);
        $this->bulk_insert_memory_usage = 0;
	}

	public function bulkInsertClearAll()
	{
	    $this->bulk_insertions = array();
        $this->bulk_insert_memory_usage = 0;
	}

    /**
     * Kiírja a várakozó adatokat
     *
     * @param string $table
     */
    public function bulkInsertDump($table)
    {
        echo "Bulk inserts into $table:\n";
        echo implode(' | ', $this->bulk_insertions[$table]['names']) . "\n";

        for($i=1; $i<=count($this->bulk_insertions[$table]['values']); $i++) {
            $values = array();
            foreach ($this->bulk_insertions[$table]['names'] as $param_name) {
                $values[] = $this->bulk_insertions[$table]['params'][':'.substr($param_name,1,strlen($param_name)-2).$i];
            }
            echo implode(' | ', $values) . "\n";
        }
        echo "\n";
    }

    /**
     * Tömeges insert-hez adatgyűjtés
     * Több táblába is gyűjthető insert, de minden táblához ugyanazokat a mezőknek kell rendelni
     *
     * @param string $table
     * @param array $columns a lementendő adatok oszlop névre kulcsolva
     * @throws Exception   ha az adott táblához begyűjtött mezőlista eltér a most beküldöttől
     */
    public function bulkDeleteCollect($table, $columns)
    {
        $this->bulk_deletions[$table]['params'][] = $columns;
    }

    /**
     * Megmondja, hogy várakoznak-e adatok lementésre
     *
     * @param string $table
     */
    public function hasBulkDeleteData($table)
    {
        return !empty($this->bulk_deletions[$table]);
    }

    /**
     * Tömeges insert-hez begyűjtött adatokat 1 SQL paranccsal egyben beszúrja
     *
     * @param string $table a tábla neve, amelyhez gyűjtött insert adatokat be kell szúrni
     */
    public function bulkDeleteExecute($table)
    {
        if (!isset($this->bulk_deletions[$table])) {
            throw new Exception('bulkDeleteExecute: deletion data for '.$table.' not found!');
        }

        $values = array();
        foreach ($this->bulk_deletions[$table]['params'] as $params) {
            $where = array();
            foreach ($params as $name => $value) {
                $name = $this->getConnection()->quoteColumnName($name);
                $where[] = is_array($value) ? $name.' IN ("'.implode('","', $value).'")' : $name .'="'.$value.'"';
            }
            $sql='DELETE FROM ' . $this->getConnection()->quoteTableName($table).' WHERE '.implode(' AND ', $where);

            $this->setText($sql)->execute();
        }
        $this->bulkDeleteClear($table);

    }

    /**
     * Az összes begyűjtött tömeges insert adatot sorban beszúrja
     * Annyi SQL parancsot hajt végre, ahány táblába gyűjtöttünk előtte adatot
     *
     */
    public function bulkDeleteExecuteAll()
    {
        foreach ($this->bulk_deletions as $table => $dummy) {
            $this->bulkDeleteExecute($table);
        }
    }

    public function bulkDeleteClear($table)
    {
        unset($this->bulk_deletions[$table]);
    }

    public function bulkDeleteClearAll()
    {
        $this->bulk_deletions = array();
    }

    /**
    * Binds a parameter to the SQL statement to be executed.
    * @param mixed $name Parameter identifier. For a prepared statement
    * using named placeholders, this will be a parameter name of
    * the form :name. For a prepared statement using question mark
    * placeholders, this will be the 1-indexed position of the parameter.
    * @param mixed $value Name of the PHP variable to bind to the SQL statement parameter
    * @param integer $dataType SQL data type of the parameter. If null, the type is determined by the PHP type of the value.
    * @param integer $length length of the data type
    * @param mixed $driverOptions the driver-specific options (this is available since version 1.1.6)
    * @return CDbCommand the current command being executed (this is available since version 1.0.8)
    * @see http://www.php.net/manual/en/function.PDOStatement-bindParam.php
    */
    public function bindParam($name, &$value, $dataType=null, $length=null, $driverOptions=null)
    {
        $c = parent::bindParam($name, $value, $dataType, $length, $driverOptions);

        // megjegyezzük a bebindolt paramétert, mert a láthatóság lekérése közben a statement újra null lesz
        $this->bound_params[] = array('name' => $name, 'value' => $value, 'dataType' => $dataType, 'length' => $length, 'driverOptions' => $driverOptions);

        return $c;
    }

    /**
     * Binds a value to a parameter.
     * @param mixed $name Parameter identifier. For a prepared statement
     * using named placeholders, this will be a parameter name of
     * the form :name. For a prepared statement using question mark
     * placeholders, this will be the 1-indexed position of the parameter.
     * @param mixed $value The value to bind to the parameter
     * @param integer $dataType SQL data type of the parameter. If null, the type is determined by the PHP type of the value.
     * @return CDbCommand the current command being executed (this is available since version 1.0.8)
     * @see http://www.php.net/manual/en/function.PDOStatement-bindValue.php
     */
    public function bindValue($name, $value, $dataType=null)
    {
        $c = parent::bindValue($name, $value, $dataType);

        // megjegyezzük a bebindolt értéket, mert a láthatóság lekérése közben a statement újra null lesz
        $this->bound_values[] = array('name' => $name, 'value' => $value, 'dataType' => $dataType);

        return $c;
    }


    public function bindParamsToPDO()  {
        parent::prepare();
        $statement = $this->getPdoStatement();

        foreach($this->bound_params as $bound_param) {
            $name = $bound_param['name'];
            $value = $bound_param['value'];
            $dataType = $bound_param['dataType'];
            $length = $bound_param['length'];
            $driverOptions = $bound_param['driverOptions'];

            if($dataType===null)
                $statement->bindParam($name,$value,$this->getConnection()->getPdoType(gettype($value)));
            else if($length===null)
                $statement->bindParam($name,$value,$dataType);
            else if($driverOptions===null)
                $statement->bindParam($name,$value,$dataType,$length);
            else
                $statement->bindParam($name,$value,$dataType,$length,$driverOptions);
        }
    }

    public function bindValuesToPDO()  {
        parent::prepare();
        $statement = $this->getPdoStatement();

        foreach($this->bound_values as $bound_value) {
            $name = $bound_value['name'];
            $value = $bound_value['value'];
            $dataType = $bound_value['dataType'];

            if($dataType===null)
                $statement->bindValue($name,$value,$this->getConnection()->getPdoType(gettype($value)));
            else
                $statement->bindValue($name,$value,$dataType);
        }
    }

    public function prepare() {
        VisibilityManager::getInstance()->addVisibilityCondition($this);

        parent::prepare();
    }

}