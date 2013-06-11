<?php
class LogBehavior extends CActiveRecordBehavior
{
    public $logging_columns = array();
    public $foreign_columns = array();
    public $log_model_name = "";
    public $link_id_col_name = "link_id";
    public $required = array();

    private $original_data = array();

    public function beforeSave($event){
        if(isset(Yii::app()->user)) {
            if(!$this->owner->isNewRecord){//ha nem új record
                $class_name = get_class($this->owner);

                $this->original_data = $class_name::model()->findByPk($this->owner->id);
            }
        }
        return parent::beforeSave($event);
    }

    public function afterSave($event)
    {
        if(isset(Yii::app()->user)) {
            $availableColumns = array_keys($this->owner->tableSchema->columns);
            if(!$this->owner->isNewRecord){//ha nem új record
                $legal_columns = array_intersect($this->logging_columns, $availableColumns);
                $link_col = $this->link_id_col_name;

                foreach($legal_columns AS $col){
                    if($this->owner->{$col} != $this->original_data->$col){
                        if(!is_null($this->owner->{$col}) || (((is_null($this->owner->{$col}) ||  $this->owner->{$col}=='') && !in_array($this->owner->{$col}, $this->required)))){
                        //if(){
                            $log = new $this->log_model_name();
                            $log->$link_col = $this->owner->id;
                            $log->attr_name = $col;
                            $log->old_value = $this->original_data->$col;
                            $log->new_value = $this->owner->{$col};
                            $log->created_by = Yii::app()->user->id;
                            $log->created_at = sfDate::getInstance()->formatDbDateTime();

                            if(array_key_exists($col, $this->foreign_columns)){
                                $foreign_col_data = $this->foreign_columns[$col];

                                if(isset($foreign_col_data['model'])){
                                    $old = $foreign_col_data['model']::model()->findByPk($this->original_data->$col);
                                    $new = $foreign_col_data['model']::model()->findByPk($this->owner->{$col});

                                    if(!is_null($old)) $log->old_value = $old->$foreign_col_data['column'];
                                    if(!is_null($new)) $log->new_value = $new->$foreign_col_data['column'];
                                }
                                elseif(isset($foreign_col_data['class'])){
                                    $data = $foreign_col_data['class']::$foreign_col_data['function']();
                                    $log->old_value = $data[(int)$this->original_data->$col];
                                    $log->new_value = $data[(int)$this->owner->{$col}];
                                }
                            }

                            $log->save();
                        }
                    }
                }

            }

        }
        return parent::afterSave($event);
    }
}
