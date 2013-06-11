<?php

class HEmailValidator extends CEmailValidator {
    protected function validateAttribute($object,$attribute)
	{
		$value=$object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value))
			return;
		if(!$this->validateValue($value))
		{
			$message=$this->message!==null?$this->message:Yii::t('msg','A {attribute} mező nem érvényes e-mail címet tartalmaz!');
			$this->addError($object,$attribute,$message);
		}
	}
}