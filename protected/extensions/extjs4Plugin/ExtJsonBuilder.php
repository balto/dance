<?php

class ExtJsonBuilder
{
  const
    LBR      = "\n",
    LBR_CM   = ",\n",
    LBR_SM   = ";\n",
    LBR_CB_L = "{\n",
    LBR_CB_R = "\n}",
    LBR_SB_L = "[\n",
    LBR_SB_R = "\n]",
		
		// build options
		BUILD_AS_RAW = 0x01,
		BUILD_AS_ARRAY = 0x02,
		BUILD_AS_OBJECT = 0x04;

	protected static $_listAttributes = array(
		"items", 
		"tbar", 
		"bbar",
		"dockedItems",
		"filter",
		"buttons", 
		"plugins", 
		"columns", 
		"view", 
		"fields", 
		"tools", 
		"actions",
		"sorters",
		"data",
        "axes",
        "series",
	);
	protected static $_quoteExcept = array(
		"key"   => array(
			"renderer", "store", "defaults", "plugins", "cm", "ds", "view", 
			"tbar", "bbar", "scope", "key", "parentPanel", "handler", "sorters", "bbar"
		),
		"value" => array(
			"true", "false", "new Ext.", "function", "Ext.", "__(", "{", "this."
		)
	);

	public static function build($config, $options = self::BUILD_AS_OBJECT)
	{
		$attributes = "";
		
		if (is_array($config)) {
			foreach ($config as $name => $value)
			{
				if ($attributes != "") {
					$attributes .= self::LBR_CM;
				}
				$attributes .= self::buildAttribute($name, $value);
			}
		}
		else {
			return self::quote(null, $config);
		}
		
		if ($options & self::BUILD_AS_OBJECT) {
			return self::LBR_CB_L.$attributes.self::LBR_CB_R;		
		}
		elseif ($options & self::BUILD_AS_ARRAY) {
			return self::LBR_SB_L.$attributes.self::LBR_SB_R;		
		}
		else {
			return $attributes;
		}
	}
	
	public static function buildAttribute($name, $value)
	{	
		if (!is_numeric($name)) {
			return sprintf("%s: %s", $name, self::quote($name, $value));
		}
		else {
			return self::quote($name, $value);
		}		
	}

  /**
   * checks if $arr is a simple Array (contains
   * a list of komma-separated values)
   *
   * @param Array
   */
  public static function isSimpleArray($arr)
	{
		foreach ($arr as $key => $value) {
			if (!is_numeric($key)
					|| is_array($value)
					|| is_object($value))
			{
				return false;
			}
		}
		return true;
  }

  /**
   * quotes everything except:
   *   values that are arrays
   *   values that are Extjs4Var
   *   values and keys that are listed in extjs4_quote_except
   *
   * @param string key
   * @param string value
   * @return string attribute
   */
  protected static function quote($key, $value)
  {
    if (is_array($value))
		{
      if (self::isSimpleArray($value)){
        if ($value == null) {
          return "[]";
        }
        else {
          return $attribute = "['".implode("','",$value)."']";
        }
      }

      $attribute = "";
      foreach ($value as $k => $v)
      {
        if (!is_numeric($k)) {
          $attribute .= sprintf("%s%s: %s", ($attribute == "" ? "" : self::LBR_CM), $k, self::quote($k, $v));
        }
        else
        {
          $attribute .= sprintf("%s%s", ($attribute == "" ? "" : self::LBR_CM), self::quote($k, $v));
        }
      }

      // test if key is one of the special list-attributes
      if (in_array($key, self::$_listAttributes) && ($key!==0)) //don't know why 0 is a match
      {
        $attribute = sprintf("[ %s ]", $attribute);
      }
      else
      {
        $attribute = sprintf("{ %s }", $attribute);
      }
			
      return $attribute;
		}
		
		
    if (is_bool($value)) {
      $attribute = $value ? "true" : "false";
      return $attribute;
    }
		
		if ($value instanceof ExtWidget) {
//			if ($value->isDefined()) {
//				return $value->ref();
//			}
//			else {
				return $value->getMembersAsJson();
//			}
		}
		if ($value instanceof ExtClass) {
			if ($value->isDefined()) {
				$value = $value->ref();
			}
			else {
				$value = $value->defineClass();
			}
		}
		elseif ($value instanceof ExtFunction) {
			if ($value->isAnonymous() || !$value->isDefined()) {				
				$value = $value->getSourceCode();
			}
			elseif ($value->isDefined()) {
				$value = $value->ref()->render(false);
			}
		}
		elseif ($value instanceof ExtVariable) {
			if ($value->isDefined()) {
				$value = $value->val();
			}
			else {
				$value = json_encode($value->getValue());
			}
		}
		elseif ($value instanceof ExtConfig) {
			$value = $value->getConfigAsJson();
		}
		elseif ($value instanceof ExtCodeFragment) {
			$value = $value->getSourceCode();
	    if ($key == "model" && strpos($value, "Ext.") === false) {
				$value = "'".$value."'";
			}
			return $value;
		}
		elseif (is_null($value)) {
      return "null";
    }
		elseif (!is_numeric($value) && self::quoteExcept($key, $value))
    {
      return "'".addslashes($value)."'";
    }

    return $value;
  }
	
  /**
   * @param string key
   * @param string value
   * @return boolean quote
   */
  private static function quoteExcept($key, $value)
  {
		if ($key == "extend") {
			return true;
		}
		
    if (is_int($key) || is_int($value)) {
      return false;
    }

    if (in_array($key, self::$_listAttributes)) {
      return false;
    }

    foreach (self::$_quoteExcept["key"] as $except)
    {
      if ($key == $except) {
        return false;
      }
    }

    foreach (self::$_quoteExcept["value"] as $except)
    {
			if (substr($value, 0, strlen($except)) == $except) {
				return false;
			}
    }

    return true;
  }
}
