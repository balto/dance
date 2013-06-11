<?php

class ExtCodetableForm extends ExtCustomWidget
{
	protected
		$_formModel, // instanceof EFormModel
		$_record, // instance of CActiveRecord
		$_module, // instanceof ExtModule
		$_controller, // current web controller instance
		$_relations; // active record relations, hashed to foreignKey name
	
	public function setup(ExtModule $module, EFormModel $formModel)
	{
		$this->_formModel = $formModel;
		$this->_module = $module;
		$this->_controller = Yii::app()->getController();		
		$this->_record = $this->_formModel->getActiveRecord();
		
		$this->_relations = array();
		foreach ($this->_record->relations() as $r) {
			// hash: foreignKey, value: className
			$this->_relations[$r[2]] = $r[1];
		}
	}
	
	protected function init()
	{		
		// basic model for combo boxes
		$this->_module->createModel("Basic", $this->_controller->getBasicSelectFieldDefinitions());
		$this->renderForm();
	}
			
	protected function renderForm()
	{
		$tableSchema = $this->_record->getTableSchema();
		
		$systemFields = array(
			"id", "created_by", "created_at", "updated_by", "updated_at",
		);
				
		foreach ($tableSchema->columns as $col) {
			if (!in_array($col->name, $systemFields)) {
				$w = $this->createInputWidget($col);
				if ($w !== null) {
					$this->add($w);
				}
			}
		}
		
		$this
			// hidden fields
			->add(Ext::Hidden($this->_formModel->generateName($this->_formModel->getCSRFFieldname()))->value($this->_formModel->generateCsrfToken()))
			->add(Ext::Hidden($this->_formModel->generateName("id")))
			// visual settings
			->layout('form')
			->border(false)
			->defaults(array("labelWidth" => 170, "anchor" => "100%"))
			->bodyPadding(5)
			->url(ExtProxy::createUrl("save", $this->_controller))
		;		
	}
	
	/**
	 * @param CMysqlColumnSchema   column schema
	 * @return ExtWidget
	 */
	protected function createInputWidget(CMysqlColumnSchema $column)
	{	
		$mandatory = $column->allowNull != 1;
		
		// field type by column schema, 
		// sequence of conditions is important!
		
		// checkbox
		if ($column->type == "integer" && $column->precision == 1) {
			return Ext::Checkbox($this->_formModel->generateName($column->name))
				->fieldLabel($this->_record->getAttributeLabel($column->name))
				->inputValue(1)
				->uncheckedValue(0)
				->allowBlank(!$mandatory)
			;
		}
		// combo box
		elseif ($column->isForeignKey) {
			// store
			
			$modelName = "Basic";
			$selectionFieldName = "name";
			// rendhagyo model osztalyok kezelese
			$extraModels = array(
				"TimeGrid" => array("nights, start_day, start_weekday, turn_in_year"),
				"RotateLocTable" => array("rotate_cycle"),					
			);
			if (array_key_exists($this->_relations[$column->name], $extraModels)) {
				$modelName = $this->_relations[$column->name];
				$selectionFieldName = $extraModels[$this->_relations[$column->name]];
				$this->createModel($modelName);
			}			
			
			$this->_module->createStore($column->name)
				->model($this->_module->model($modelName))
				->autoLoad(true)
				->remoteSort(true)
				->proxy(Ext::Proxy()->url("getComboList", $this->_controller)
					->extraParams(array(
						"model" => $this->_relations[$column->name], 
						"active_only" => 0, // TODO: új felvitelekor  "active_only"-t nm kell átadni
						"selection_field_name" => $selectionFieldName,
						"first_empty" => !$mandatory,
					))
					->reader(Ext::JsonReader())
				)
			;
			return Ext::ComboBox($this->_formModel->generateName($column->name))
				->fieldLabel($this->_record->getAttributeLabel($column->name))
				->store($this->_module->store($column->name))
				->allowBlank(!$mandatory)
				->editable(false)
				->forceSelection(true)
				->displayField('name')
				->valueField('id')
			;
		}
		// number field
		elseif ($column->type == "integer") {
			return Ext::NumberField($this->_formModel->generateName($column->name))
				->fieldLabel($this->_record->getAttributeLabel($column->name))
				->allowBlank(!$mandatory)
			;
		}
		// textarea
		elseif ($column->type == "string" && $column->size === null) {
			return Ext::TextArea($this->_formModel->generateName($column->name))
				->fieldLabel($this->_record->getAttributeLabel($column->name))
				->allowBlank(!$mandatory)
				->rows(10)
			;				
		}
		// text field
		else {
			return Ext::TextField($this->_formModel->generateName($column->name))
				->fieldLabel($this->_record->getAttributeLabel($column->name))
				->allowBlank(!$mandatory)
			;
		}
		
		return null;
	}
	
	protected function createModel($modelName)
	{
		if (class_exists($modelName)) {
			$fieldDefinitions = array(array("name" => "name"));
			foreach ($modelName::model()->getTableSchema()->columns as $col) {
				$fieldDefinitions[] = array(
					"name" => $col->name
				);
			}
			$this->_module->createModel($modelName, $fieldDefinitions);
		}
	}
	
	
}
