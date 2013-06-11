<?php

class ExtGrid extends ExtWidget
{
	protected
		$_rowActions;

	public function __construct($name, $classdesc, ExtClass $context = null)
	{
		parent::__construct($name, $classdesc, $context);

		// if columns option not exists, create an empty configuration
		if ($this->columns === null) {
			$this->columns = new ExtFieldConfig($this);
		}

		$this->_rowActions = null;
	}

	/**
	 * If strore specified, try to extract columns options
	 */
	public function store(ExtStore $store)
	{
		if ($store->getModel() !== null) {
			$this->columns(new ExtFieldConfig($store->getModel()->getFieldConfig()->extractGridColumns(), $this));
		}

		return parent::store($store);
	}
	
	/**
	 * Group columns
	 * 
	 * @param string   group title
	 * @param string[] name of fields in group
	 * @return ExtGrid
	 */
	public function header($name, $columns)
	{
		$group = null;
		$columnsInGroup = array();
		
		try {
			$group = $this->columns->getField($name);
			$columnsInGroup = $group["columns"];
		}
		catch (Exception $e) {
			$columnsInGroup = array($this->columns->getField($columns[0]));
			$group = $this->columns->replaceField($columns[0], array(
				"text" => $name,
				"columns" => array()
			));
			array_shift($columns);
		}

		if (!empty($columns)) {
			foreach ($columns as $name) {
				$columnsInGroup[] = $this->columns->getField($name);
				$this->columns->removeField($name);
			}
		}
				
		$group["columns"] = $columnsInGroup;
		
		return $this;
	}
	
	/**
	 * add column to columns configuration
	 */
	public function column($header, $dataindex = "", $params = array())
	{
		if ($header instanceof ExtRowActions) {
			$rowactions = $header;
			return $this->rowaction($rowactions);
		}
		else {

			if (is_array($dataindex)) {
				$params = $dataindex;
			}
			else {
				$params["dataIndex"] = $dataindex;
			}

			$params["header"] = $header;

			return $this->addColumn($header, $params);
		}
	}

	protected function addColumn($header, $params = array(), $overwrite = false)
	{
		// $this->_config["fields"] is ExtFieldConfig instance!
		return $this->columns->addField($header, $params, $overwrite);
	}

	public function rowaction(ExtClass $rowaction)
	{

		if ($rowaction instanceof ExtRowActions) {
			$this->_rowActions = $rowaction;
		}
		else {
			if ($this->_rowActions === null) {
				$this->_rowActions = Ext::RowActions();
			}
			$this->_rowActions->action($rowaction);
		}


		$this->columns->addField(null, $this->_rowActions->getMembersAsArray(), true/*overwrite existing one!*/);

		return $this;
	}

	/**
	 *
	 * Gets the field config by the given field name
	 * @param string $fieldName
	 */
	public function getFieldConfig($fieldName) {
	    return $this->columns->getField($fieldName);
	}

	/**
	 *
	 * Gets the filter config by the given field name
	 * @param string $fieldName
	 */
	 public function getFilter($fieldName) {
	    $fieldConfig = $this->getFieldConfig($fieldName);
	    if ($fieldConfig && isset($fieldConfig['filter'])) {
	        return $fieldConfig['filter'];
	    } else {
	        return null;
	    }
	}

}
