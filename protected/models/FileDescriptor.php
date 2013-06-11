<?php

/**
 * This is the model class for table "file_descriptor".
 *
 * The followings are the available columns in table 'file_descriptor':
 * @property string $id
 * @property string $file_id
 * @property string $file_name
 * @property string $mime_type
 * @property string $size
 * @property integer $created_by
 * @property string $description
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property FileStore $file
 * @property User $createdBy
 */
class FileDescriptor extends CActiveRecord
{
    private static $allowed_mime_types = array(
        array(
            'name'=>array('text/plain'),
            'display_name'=>'Szöveges fájl',
            'icon'=>'icon_txt',
            'extensions'=>array('txt'),
            'max_size'=>1048576,
        ),
        array(
            'name'=>array('image/gif'),
            'display_name'=>'GIF kép',
            'icon'=>'icon_gif',
            'extensions'=>array('gif'),
            'max_size'=>1048576,
        ),
        array(
            'name'=>array('image/jpeg', 'image/pjpeg'),
            'display_name'=>'JPG kép',
            'icon'=>'icon_jpg',
            'extensions'=>array('jpg','jpeg'),
            'max_size'=>2097152,
    		//'max_size'=>1097152,
        ),
        array(
            'name'=>array('image/png', 'image/x-png'),
            'display_name'=>'PNG kép',
            'icon'=>'icon_png',
            'extensions'=>array('png'),
            'max_size'=>1048576,
        ),
        array(
            'name'=>array('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'display_name'=>'Word dokumentum',
            'icon'=>'icon_doc',
            'extensions'=>array('doc', 'docx', 'rtf'),
            'max_size'=>2097152,
        ),
        array(
            'name'=>array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/comma-separated-values'),
            'display_name'=>'Excel táblázat',
            'icon'=>'icon_xls',
            'extensions'=>array('xls', 'xlsx', 'csv'),
            'max_size'=>2097152,
        ),
        array(
            'name'=>array('application/pdf', 'application/rfc822'),
            'display_name'=>'PDF dokumentum',
            'icon'=>'icon_pdf',
            'extensions'=>array('pdf'),
            'max_size'=>2097152,
        ),
    );


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FileDescriptor the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'file_descriptor';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('file_id, file_name, mime_type, size, created_by, created_at', 'required'),
			array('created_by', 'numerical', 'integerOnly'=>true),
			array('file_id, size', 'length', 'max'=>20),
			array('file_name, mime_type', 'length', 'max'=>128),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, file_id, file_name, mime_type, size, created_by, description, created_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'file' => array(self::BELONGS_TO, 'FileStore', 'file_id'),
			'createdBy' => array(self::BELONGS_TO, 'User', 'created_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'file_id' => 'Fájl',
			'file_name' => 'Fájl név',
			'mime_type' => 'Mime típus',
			'size' => 'Méret',
			'created_by' => 'Készítette',
			'description' => 'Leírás',
			'created_at' => 'Készült',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('file_id',$this->file_id,true);
		$criteria->compare('file_name',$this->file_name,true);
		$criteria->compare('mime_type',$this->mime_type,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created_at',$this->created_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function behaviors(){
	    return array(
            'Blameable' => array(
                'class'=>'ext.behaviors.BlameableBehavior',
            ),
	    );
	}

	public function getMaxSizesByExt() {
	    $result = array();
	    foreach(self::$allowed_mime_types as $mime_type) {
	        foreach($mime_type['extensions'] as $ext) {
	            $result[$ext] = $mime_type['max_size'];
	        }
	    }

	    return $result;
	}

	public static function getMaxSizesByMimeTypeNames() {
	    $result = array();
	    foreach(self::$allowed_mime_types as $mime_type) {
	        foreach($mime_type['name'] as $name) {
	            $result[$name] = $mime_type['max_size'];
	        }
	    }

	    return $result;

	}

	public function getFileIconInfoByExt() {
	    $result = array();
	    foreach(self::$allowed_mime_types as $mime_type) {
	        foreach($mime_type['extensions'] as $ext) {
	            $result[$ext] = array(
	                    'file_name' => $mime_type['icon'].'.png',
	                    'title' => $mime_type['display_name']
	            );
	        }
	    }

	    return $result;
	}

}