<?php

class ExtFieldConfig extends ExtConfig
{
	protected
		$_fieldName,
		$_fieldMap,
		$_raw,
		$_checkOption; // if given field (config) option is invalid for Ext.data.Field, throws an exception

	public static $_fieldOptions = array(
		"convert", "dateFormat", "defaultValue", "mapping", "name",
		"persists", "sortDir", "sortType", "type", "useNull"
	);

	public function __construct($name = null, $field_definition = null, ExtClass $context = null, $check_option = false)
	{
		// constructor overloading
		// new ExtFieldConfig($field_definition, ...)
		if (is_array($name)) {
			$check_option = $context===null?false:$context;
			$context = $field_definition;
			$field_definition = $name;
			$name = null;
		}
		elseif ($name instanceof ExtClass) {
			$check_option = $field_definition;
			$context = $name;
			$field_definition = null;
			$name = null;
		}
		if ($context !== null && !($context instanceof ExtClass)) {
			throw new ExtException("Context must be an instance of ExtClass!");
		}

		parent::__construct($context);

		$this->_raw = array();
		$this->_fieldMap = array();
		$this->_fieldName = $name;
		$this->_checkOption = $check_option;

		if (!empty($field_definition)) {
			foreach ($field_definition as $field) {
				$this->addField($field["name"], $field);
			}
		}
	}

	public function addField($name, $params = null, $overwrite = false)
	{
		if (!$overwrite && array_key_exists($name, $this->_fieldMap)) {
			throw new ExtException(sprintf("Field %s is already defined!", $name));
		}


//		// if config given, get as array
//		if ($params instanceof ExtConfig) {
//			$params = $params->getConfigAsArray();
//		}
//		// if clas config given, get the config as array
//		elseif ($params instanceof ExtClassMembers) {
//			$params = $params->getAsArray();
//		}
		// method overloading:
		// if second argument is string, treat as "type" of field
		elseif (!is_array($params) && !is_object($params) && $params != "") {
			$params = array("type" => $params);
		}


		$config = new ExtFieldConfig($name, array(), $this->_context, $this->_checkOption);
		$config->addOption("name", $name);

		foreach ($params as $key => $value) {
			$config->addOption($key, $value);
		}

		$this->addFieldConfig($name, $config, $overwrite);

		if (!array_key_exists($this->_fieldMap[$name], $this->_raw)) {
			$this->_raw[$this->_fieldMap[$name]] = array();
		}

		$this->_raw[$this->_fieldMap[$name]] = array_merge($this->_raw[$this->_fieldMap[$name]], $config->getRaw());
		
		return $config;
	}
	
	public function replaceField($name, $params = null)
	{
		return $this->addField($name, $params, true);
	}

	/**
	 * 
	 * @param string
	 * @return ExtConfig   removed field
	 * @throws ExtException
	 */
	public function removeField($name)
	{
		$field = null;
		
		if (array_key_exists($name, $this->_fieldMap)) {
			$i = $this->_fieldMap[$name];
			unset($this->_fieldMap[$name]);
			$field = $this->_config[$i];
			
			for ($j = $i; $j < count($this->_config)-1; $j++) {
				$name = $this->_config[$j+1]["name"];
				$this->_fieldMap[$name] = $j;
				$this->_config[$j] = $this->_config[$j+1];
			}
			
			unset($this->_config[$j]);
		}
		else {
			throw new ExtException(sprintf("Undefined field: %s", $name));
		}
		
		return $field;
	}

	public function addOption($name, $value)
	{
		if ($value != "") {
			$this->_raw[$name] = $value;
			if (!($this->_context instanceof ExtModel)
				|| $this->_context instanceof ExtModel && in_array($name, self::$_fieldOptions)) {
				$this->_config[$name] = $value;
			}
			elseif ($this->_checkOption) {
				throw new ExtException(sprintf("Invalid field configuration option: %s", $name));
			}
		}
	}

	protected function addFieldConfig($name, ExtFieldConfig $config, $overwrite = false)
	{
		if ($overwrite && array_key_exists($name, $this->_fieldMap)) {
			$index = $this->_fieldMap[$name];
		}
		else {
			$index = count($this->_config);
		}

		$this->_config[$index] = $config;
		$this->_fieldMap[$name] = $index;
	}

	public function getRaw()
	{
		return $this->_raw;
	}

	public function getField($name, $raw = false)
	{
		if (array_key_exists($name, $this->_fieldMap)) {
			if ($raw) {
				return $this->_config[$this->_fieldMap[$name]]->getRaw();
			}
			else {
				return $this->_config[$this->_fieldMap[$name]];
			}
		}
		else {
			throw new ExtException(sprintf("Undefined field: %s", $name));
		}
	}

	public function getConfigAsArray($raw = null)
	{
		if ($raw === null) {
			$raw = $this->_config;
		}

		if (!is_array($raw) || empty($raw)) {
			return array();
		}

		$config = array();
		foreach ($raw as $key => $value) {
			if ($value instanceof ExtConfig) {
				$config[] = $value->getConfigAsArray();
			}
			else {
//				if ($value instanceof ExtClass) {
//					// if already defined, we just pass the reference
//					if ($value->isDefined() && $value->isRendered() || $value->isRendered()) {
//						$config[$key] = $value->ref();
//					}
//					else {
//						$config[$key] = $value->getConfig()->getConfigAsArray();
//					}
//				}

				$config[$key] = $value;
			}
		}

		return $config;
	}

	/**
	 * create ExtFieldConfig object from given fields
	 *
	 * @param string  comma separated field names. To extract all fields, just omit parameter.
	 */
	public function extract($names = null)
	{
		if ($names === null) {
			return clone $this;
		}

		$field_names = explode(",", $names);

		$config = new ExtFieldConfig(null, null, null, false);
		foreach ($field_names as $name) {
			$config->addField($name, $this->getField($name, true));
		}
		return $config;
	}

	public function extractSorters()
	{
		$sorters = array();

		foreach ($this->_config as $field) {
			if (isset($field["sortDir"]) && $field["sortDir"] != "") {
				$config = new ExtFieldConfig();
				$config["name"] = $field["name"];
				$config["property"] = $field["name"];
				$config["direction"] = $field["sortDir"];
				$sorters[] = $config;
				unset($config);
			}
		}

		return $sorters;
	}

	public function extractGridColumns()
	{
		$grid_columns = array();

		$i = 0;
		foreach ($this->_raw as $field) {
			if (isset($field["gridColumn"]) && $field["gridColumn"]) {
				$grid_columns[$i] = new ExtFieldConfig();
				$grid_columns[$i]["dataIndex"] = $grid_columns[$i]["name"] = $field["name"];
				$grid_columns[$i]["header"] = isset($field["header"])?$field["header"]:"";
				$grid_columns[$i]["sortable"] = isset($field["sortable"])?$field["sortable"]:false;
				$grid_columns[$i]["hideable"] = isset($field["hideable"])?$field["hideable"]:false;
				$grid_columns[$i]["groupable"] = isset($field["groupable"])?$field["groupable"]:false;
				$grid_columns[$i]["resizeable"] = isset($field["resizeable"])?$field["resizeable"]:false;
				$grid_columns[$i]["flex"] = isset($field["flex"])?$field["flex"]:false;
				$grid_columns[$i]["sortDir"] = isset($field["sortDir"])?$field["sortDir"]:false;
				$grid_columns[$i]["width"] = isset($field["width"])?$field["width"]:0;
                $grid_columns[$i]["editor"] = isset($field["editor"])?$field["editor"]:"";

				// xtype:
				if (isset($field["xtype"])) {
					$grid_columns[$i]["xtype"] = $field["xtype"];
				}
				// tpl:
				if (isset($field["tpl"])) {
					$grid_columns[$i]["tpl"] = $field["tpl"];
				}
				// align:
				if (isset($field["align"])) {
					$grid_columns[$i]["align"] = $field["align"];
				}
                // format:
                if (isset($field["format"])) {
                    $grid_columns[$i]["format"] = $field["format"];
                }
                // summaryType:
                if (isset($field["summaryType"])) {
                    $grid_columns[$i]["summaryType"] = $field["summaryType"];
                }
                // summaryType:
                if (isset($field["summaryRenderer"])) {
                    $grid_columns[$i]["summaryRenderer"] = $field["summaryRenderer"];
                }
				// filter:
				if (isset($field["filter"])) {
				    // set namespace on id
					if (isset($field["filter"][0]["id"])) {
					    if ($this->_context instanceof ExtComponent) {
					        if ($this->_context->getContext() instanceof ExtModule) {
    					        $field["filter"][0]["id"] = $this->_context->getContext()->getNamespace().".columnfilter.".$field["filter"][0]["id"];
    					    }
    					}
					}
				    $grid_columns[$i]["filter"] = $field["filter"];
				}
				// renderer:
				if (isset($field["renderer"])) {
					if ($field["renderer"] instanceof ExtCodeFragment) {
						$field["renderer"] = new ExtCodeFragment($field["renderer"]);
					}
					$grid_columns[$i]["renderer"] = $field["renderer"];
				}

				$i++;
			}
		}
		return $grid_columns;
	}

	/**
	 * If context is null, this method is a shorthand to addField(), else
	 * call the context's field() method.
	 */
	public function field()
	{
		if ($this->_context !== null) {
			return call_user_func_array(array($this->_context, "field"), func_get_args());
		}
		else {
			return call_user_func_array(array($this, "addField"), func_get_args());
		}
	}

	/**
	 * Shorthand to sortDir() option
	 */
	public function sort($direction = "ASC")
	{
		$this->addOption("sortDir", $direction);

		return $this;
	}

	/**
	 * Shorthand to gridColumn() option
	 */
	public function gridcolumn()
	{
		$this->addOption("gridColumn", true);

		return $this;
	}

	public function __call($method, $args)
	{
		$this->addOption($method, $args[0]);

		return $this;
	}

}