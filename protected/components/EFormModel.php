<?php
class EFormModel extends CFormModel {

    protected $name_format;
    protected $csrf_token_name = 'csrf_token';
    protected $active_record;
    protected $orig_active_record;

    /**
    * Initializes this model.
    * This method is invoked in the constructor right after {@link scenario} is set.
    * You may override this method to provide code that is needed to initialize the model (e.g. setting
    * initial property values.)
    * @since 1.0.8
    */
    public function init()
    {
        $class_name = get_class($this);
        if (!$this->name_format) $this->name_format = substr($class_name, 0, strlen($class_name)-4);
    }

    public function bindActiveRecord(CActiveRecord $active_record) {
        $this->active_record = $active_record;
        $this->orig_active_record = clone $active_record;
    }

    public function getActiveRecord() {
        return $this->active_record;
    }

    public function getOrigActiveRecord() {
        return $this->orig_active_record;
    }

    public function getNameFormat() {
        return $this->name_format;
    }

    public function generateName($name) {

        return $this->name_format . '['.$name.']';
    }

    public function getCSRFFieldname() {
        return $this->csrf_token_name;
    }


    public function getCsrfToken() {

        return Yii::app()->user->getState($this->name_format . '_' . $this->csrf_token_name);
    }

    public function generateCsrfToken() {
        // egyedi a formra és sessionbe téve
        $salt = md5(rand(100000, 999999).$this->name_format);
        $csrf_token = sha1($salt . session_id());

        Yii::app()->user->setState($this->name_format . '_' . $this->csrf_token_name, $csrf_token);

        return $csrf_token;
    }

    public function getLabel($name) {
        $label = $this->getAttributeLabel($name);
        if (!$label && $this->active_record) {
            $label =  $this->active_record->getAttributeLabel($name);
        }

        return (isset($label) ? $label : '');
    }

    public function attributeLabels()
    {
        if ($this->active_record) {
            return $this->active_record->attributeLabels();
        } else {
            return parent::attributeLabels();// Ez még nem az amit akarunk: a leszérmazott.ban definiált labelekhez hozzá merge-elni a model-ben levőket
        }
    }

    public function bind($parameters) {
        if ($this->active_record){
            foreach($parameters AS $col => &$val){
                if(!$this->active_record->isAttributeRequired($col) && $val === ''){
                    $val = null;
                }
            }
        }

        $this->attributes = $parameters;
        if ($this->active_record) $this->active_record->attributes = $parameters;
    }


    /**
    * Validates the models associated with this form.
    * All models, including those associated with sub-forms, will perform
    * the validation. You may use {@link CModel::getErrors()} to retrieve the validation
    * error messages.
    * @return boolean whether all models are valid
    */
    public function validate()
    {
        if (!$this->validateCsrfToken()) return false;

        $success = parent::validate();

        if ($this->active_record) $success = $success && $this->active_record->validate();

        return $success;
    }

    protected function validateCsrfToken() {
        $request = Yii::app()->request;

        $token_from_session = $this->getCsrfToken();

        $form_params = $request->getPost($this->name_format);
        $token_from_post = $form_params[$this->getCSRFFieldname()];

        $valid = ($token_from_session && $token_from_post && $token_from_session == $token_from_post);

        if (!$valid) $this->addError('', Yii::t('msg','Az űrlap feldolgozása nem engedélyezett!'));

        return $valid;
    }

    /**
    * Returns a value indicating whether there is any validation error.
    * @param string $attribute attribute name. Use null to check all attributes.
    * @return boolean whether there is any error.
    */
    public function hasErrors($attribute=null)
    {
        $bool = parent::hasErrors($attribute);
        if ($this->active_record) $bool = $bool || $this->active_record->hasErrors($attribute);

        return $bool;
    }

    /**
    * Returns the errors for all attribute or a single attribute.
    * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
    * @return array errors for all attributes or the specified attribute. Empty array is returned if no error.
    */
    public function getErrors($attribute=null)
    {
        $errors = parent::getErrors($attribute);

        // TODO ugy kellene merge-elni, hogy ugyanaz a kulcs ne essen ki, hanem a value merge-elődjön
        if ($this->active_record) $errors = array_merge($errors, $this->active_record->getErrors($attribute));

        return $errors;
    }

    private function hasBehavior($behavior) {
        if ($this->active_record) {
            $behavior = strtolower($behavior);
            $ar_behaviors = $this->active_record->behaviors();

            foreach ($ar_behaviors as $ar_behavior) {
                if (strpos(strtolower($ar_behavior['class']), $behavior)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function save() {
        if ($this->active_record) {
            $save_action = ($this->hasBehavior('ENestedSetBehavior'))?'saveNode':'save';

            if ($this->active_record->$save_action()){
                $this->id = $this->active_record->id;
                return true;
            }
        }
        return false;
    }

    public function appendTo(CActiveRecord $active_record){
        if ($this->active_record->appendTo($active_record)){
            return $this->active_record->id;
        }

        return false;
    }
    
    public function isGetted($param_name) {
        if ($this->active_record && !empty($this->attributes)) {
            $different = array_diff($this->getActiveRecord()->attributes, $this->attributes);
            $not_getted_params = array_keys($different);

            if(!in_array($param_name, $not_getted_params)){
                return true;
            }
            else{
                return false;
            }
        }
    }
}