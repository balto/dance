<?php

class MainFileManager extends BaseModelManager
{
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MainFileManager();
        }
        return self::$instance;
    }

    public function processFile($file_data){
        $tmp_name = $file_data["tmp_name"];

        if (empty($tmp_name)) return null;

        $file_content = file_get_contents($tmp_name);

        $new_file = new FileStore();
        $new_file->file = $file_content;

        if($new_file->save()){
            $file_name = $file_data["name"];
            $file_type = $file_data["type"];
            $file_size = $file_data["size"];

            $new_description = new FileDescriptor();
            $new_description->file_id = $new_file->id;
            $new_description->file_name = $file_name;
            $new_description->mime_type = $file_type;
            $new_description->size = $file_size;

            if($new_description->save()){
                return $new_file->id;
            }
        }
    }

    public function deleteFile($file_id, $meter_id, $col_name) {
        FileStore::model()->deleteByPk($file_id);
        FileDescriptor::model()->deleteAll("file_id =:file_id", array(":file_id"=>$file_id));

        if(Meter::model()->updateByPk($meter_id, array($col_name => null))){
            return true;
        }
        else{
            return false;
        }
    }

}