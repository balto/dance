<?php

class Ext
{
	protected
		$_controller, // Yii controller class
		$_classes, // Extjs classes (instances of ExtClass classes)
		$_classMap, // maps of classes by name
		$_functions, // js functions
		$_functionMap, // maps of js functions
		$_variables, // js variables
		$_variableMap, // map of js variables
		$_autoRender,
		$_allowImplicitCalls;
	
	protected static $_widgets = array();
		
	public function __construct($controller = null, $config = array())
	{
		$this->_controller = $controller;
		$defaults = array(
			"autoRender" => false,
			"allowImplicitCalls" => true,
		);
		$config = array_merge($defaults, $config);
		$this->_autoRender = $config["autoRender"];
		$this->_allowImplicitCalls = $config["allowImplicitCalls"];
		
		$this->_classes = $this->_classMap = array();
		$this->_functions = $this->_functionMap = array();
		$this->_variables = $this->_variableMap = array();		
	}
	
	public function isAutoRender() { return $this->_autoRender; }	
			
	/**
	 * Creates class or call the named function if exists.
	 * 
	 * @return ExtClass|ExtFunction
	 */
	public function __call($method, $arguments)
	{
		$classdesc = self::getClassConfig($method);
		if ($classdesc !== null) {
			$name = !empty($arguments)?$arguments[0]:"";//strtolower($classdesc[ExtClasses::PHPCLASS]).uniqid();
			// if class already defined and allowed implicit calls
			// return the class instance
			if (($class = $this->getClass($name)) !== null) {
				if ($this->_allowImplicitCalls) {
					return $class;
				}
				else {
					throw new ExtException("Class already defined!");
				}
			}
			
			// the remain arguments we pass to custom widget's init() method
			array_shift($arguments);
			$initParams = $arguments;
			if (!empty($initParams)) {
				$class = new $classdesc[ExtClasses::PHPCLASS]($name, $classdesc, null, $initParams);
			}
			else {
				$class = new $classdesc[ExtClasses::PHPCLASS]($name, $classdesc);
			}

			
			if ($name != "" && $class instanceof ExtClass) {
				$this->add($class);
			}
			
			return $class;
		}
		// call specified function
		else {
			if (array_key_exists($method, $this->_functionMap)) {
				if ($this->_allowImplicitCalls) {
					return call_user_func_array(array($this->_functions[$this->_functionMap[$method]], "call"), $arguments);
				}
				else {
					throw new ExtException("Implicit function calls are not allowed! Use instead ExtFunction::call() method.");
				}
			}
			// if function not exists, create it
			else {
				// function body mandatory, if has parameters they are in first argument:
				$body = "";
				$name_and_parameters = $method;
				if (count($arguments) == 0) {
					throw new ExtException("Missing function body: {$method}()!");
				}
				elseif (count($arguments) == 1) {
					$body = $arguments[0];					
				}
				elseif (count($arguments) == 2) {
					$parameters = trim($arguments[0], " ()");
					$name_and_parameters = $method."(".$parameters.")";
					$body = $arguments[1];
				}
				else {
					throw new ExtException("Too many arguments!");
				}

				return $this->fn($name_and_parameters, $body);

//				throw new ExtException(sprintf("Function %s() is not defined!", $method));
			}
		}
	}
	
	private static function getClassConfig($name)
	{
		$classes = new ExtClasses();
		
		$config_key = null;
		$config = null;
		
		// proxy
		if (strpos($name, "Proxy") !== false && array_key_exists($name, $classes["proxy"])) {
			$config_key = "proxy";
			$phpclass = "ExtProxy";
		}
		// reader
		if (strpos($name, "Reader") !== false && array_key_exists($name, $classes["reader"])) {
			$config_key = "reader";
			$phpclass = "ExtReader";
		}
		// store
		if (strpos($name, "Store") !== false && array_key_exists($name, $classes["store"])) {
			$config_key = "store";
			$phpclass = "ExtStore";
		}
		// widget
		elseif (array_key_exists($name, $classes["widget"])) {
			$config_key = "widget";
			$phpclass = "ExtWidget";
		}
		
		if ($config_key !== null) {
			if ($classes[$config_key][$name][ExtClasses::PHPCLASS] !== null) {		
				$phpclass = $classes[$config_key][$name][ExtClasses::PHPCLASS];
			}
			$config = array(
				ExtClasses::PHPCLASS => $phpclass,
				ExtClasses::EXTCLASS => $classes[$config_key][$name][ExtClasses::EXTCLASS],
				ExtClasses::XTYPE => $classes[$config_key][$name][ExtClasses::XTYPE],
				ExtClasses::DEFAULT_CONFIG => $classes[$config_key][$name][ExtClasses::DEFAULT_CONFIG],
			);
		}
		
		return $config;
	}
	
	/**
	 * Creates classes or functions statically, means that the component
	 * is not belongs to this Ext instance. If given classname not exists in ExtClasses
	 * then create function instead.
	 * 
	 * Only classes or functions  can create statically, to create variables statically
	 * use the appropriate class constructor.
	 * 
	 * call examples:
	 * 
	 * Ext::Model();
	 * Ext::Model($context);
	 * Ext::Model($modelName);
	 * Ext::Model($modelName, $context);
	 * Ext::MyCustomWidget($context, array $initOptions);
	 * Ext::MyCustomWidget(array $initOptions);
	 * 
	 * @return ExtClass
	 */
	public static function __callStatic($method, $arguments)
	{
		$classdesc = self::getClassConfig($method);
		if ($classdesc !== null) {
			$name = "";
			$context = null;
			$initParams = array(); // custom widget init() method parameters
			if (!empty($arguments)) {
				if ($arguments[0] instanceof ExtClass) {
					$context = $arguments[0];
					array_shift($arguments);					
				}
				elseif (is_string($arguments[0])) {
					$name = $arguments[0];
					array_shift($arguments);
				}
				
				if (count($arguments) == 2 && $arguments[1] instanceof ExtClass) {
					$context = $arguments[1];
					array_shift($arguments);
				}
				
				// the remain arguments we pass to custom widget's init() method
				if (!empty($arguments)) {
					$initParams = $arguments;
				}
			}
			
			if (!empty($initParams)) {
				$widget = new $classdesc[ExtClasses::PHPCLASS]($name, $classdesc, $context, $initParams);
			}
			else {
				$widget = new $classdesc[ExtClasses::PHPCLASS]($name, $classdesc, $context);
			}
						
			if ($name != "" && strpos($name, ".") === false && strpos($name, "/") === false) {
				self::$_widgets[$name] = $widget;
			}
			
			return $widget;
		}
		// function creation
		else {
			// function body mandatory, if has parameters they are in first argument:
			$body = "";
			$name_and_parameters = $method;
			if (count($arguments) == 0) {
				throw new ExtException("Missing function body: {$method}()!");
			}
			elseif (count($arguments) == 1) {
				$body = $arguments[0];					
			}
			elseif (count($arguments) == 2) {
				$parameters = trim($arguments[0], " ()");
				$name_and_parameters = $method."(".$parameters.")";
				$body = $arguments[1];
			}
			else {
				throw new ExtException("Too many arguments!");
			}
			
			return self::createFunction($name_and_parameters, $body);
		}
	}
	
	public function w($name)
	{
		if (array_key_exists($name, self::$_widgets)) {
			return self::$_widgets[$name];
		}
		else {
			throw new ExtException("Widget not exists: {$name}");
		}
	}
	
	public function __get($name)
	{
		if (!$this->_allowImplicitCalls) {
			throw new ExtException("Implicit calls are not allowed! Use instead the appropriate get... method.");
		}
		
		$class = $this->getClass($name);
		if ($class !== null) {
			return $class;
		}
		$function = $this->getFunction($name);
		if ($function !== null) {
			return $function;
		}
		$variable = $this->getVariable($name);
		if ($variable !== null) {
			return $variable;
		}
		
		throw new ExtException("Undefined class or variable or function!");
	}
	
	/**
	 * creates function
	 * 
	 * @return ExtFunction $function
	 */
	public static function fn()
	{
		$function = call_user_func_array(array("Ext", "createFunction"), func_get_args());
		
		return $function;
	}
	
	protected static function createFunction()
	{
		$args = func_get_args();
		$name = "";
		$parameters = array();
		$body = "";
		// anonymous funcion, only function body supplied,
		// or if function exists with given name, return function instance
		if (count($args) > 0) {
			// first parameter is the function body
			if (strpos($args[0], ".") !== false) {
				$body = $args[0];
			}
			else {
				$name_and_parameters = explode("(", $args[0]);
				$name = $name_and_parameters[0];
				// ha csak parameterek vannak ,-vel elvalasztva, akkor
				// az nem a fuggveny neve, hanem a parameterei
				if (strpos($name, ",") !== false) {
					$name_and_parameters[1] = $name;
					$name = "";
				}

				$parameters = array();
				if (count($name_and_parameters) > 1) {
					// truncate ')' end of the parameter string
					if (strrpos($name_and_parameters[1], ")") == strlen($name_and_parameters[1])-1) {
						$name_and_parameters[1] = substr($name_and_parameters[1], 0, -1);
					}
					$parameters = array_map("trim", explode(",", trim($name_and_parameters[1])));
				}
			}
		}
		
		if (count($args) > 1) {
			$body = $args[1];
		}
		return new ExtFunction($name, $parameters, $body);		
	}
	
	public function createModel($name, $field_config = null)
	{
		$model = new ExtModel($name, array(
			ExtClasses::EXTCLASS => "Ext.data.Model",
			ExtClasses::DEFAULT_CONFIG => array("fields" => $field_config)
		), $this);
		
		return $this->add($model);
	}
		
	/**
	 * Starts function declaration. Use in conjunction Ext::endFunction().
	 * 
	 * @param string   function name
	 * @return ExtFunction  function being created
	 */
	public function beginFunction($name)
	{
		if ($name == "") {
			throw new ExtException("Function name is mandatory!");
		}
		
		$function = $this->fn($name, "//creating function body");
		$function->begin();
		return $function;
	}
	
	/**
	 * Finish function declaration. Use in conjunction Ext::beginFunction().
	 * 
	 * @return ExtFunctiun  function declared
	 */
	public function endFunction()
	{
		$function = $this->_functions[count($this->_functions)-1];
		$function->end();
		return $function;
	}
	
	/**
	 * shorthand to Ext::variable()
	 * 
	 * @return ExtVariable  variable declared
	 */
	public function v()
	{
		return call_user_func_array(array($this, "variable"), func_get_args());
	}
	
	/**
	 * creates variable
	 * 
	 * @return ExtVariable
	 */
	public function variable()
	{
		$args = func_get_args();
		$name = $args[0];
		$value = null;
		
		if (count($args) > 1) {
			$value = $args[1];
		}
		
		$variable = new ExtVariable($name, $value);
		
		if ($value !== null) {
			$this->add($variable);
		}
		
		return $variable;
	}

	/**
	 * creates code fragment
	 * 
	 * @return ExtCodeFragment
	 */
	public function code()
	{
		$args = func_get_args();
		$name = "";
		$code = "";
		// anonymous code fragment
		if (count($args) == 1) {
			$code = $args[0];
		}
		elseif (count($args) > 1) {
			$name = $args[0];
			$code = $args[1];
		}
		
		return new ExtCodeFragment($name, $code, $this);
	}
	
	public function add(ExtCodeFragment $component) 
	{
		$component_name = $component->getName();
		if ($component_name == "") {
			throw new ExtException("Component name must be specified!");
		}
		
		if ($component instanceof ExtVariable) {
			if (isset($this->_variableMap[$component_name])) {
				throw new ExtException(sprintf("Variable %s already defined!", $component_name));
			}
			$component->setContext($this);
			$index = count($this->_variables);
			$this->_variables[$index] = $component;
			$this->_variableMap[$component_name] = $index;
		}
		elseif ($component instanceof ExtFunction) {
			if (isset($this->_functionMap[$component_name])) {
				throw new ExtException(sprintf("Function %s() already defined!", $component_name));
			}
			$component->setContext($this);
			$index = count($this->_functions);
			$this->_functions[$index] = $component;
			$this->_functionMap[$component_name] = $index;
			
		}
		elseif ($component instanceof ExtClass) {
			if (isset($this->_classMap[$component_name])) {
				throw new ExtException(sprintf("Component %s already defined!", $component_name));
			}
			$component->setContext($this);
			$index = count($this->_classes);
			$this->_classes[$index] = $component;
			$this->_classMap[$component_name] = $index;	
		}
		
		return $component;
	}
	
	////////////////////////////////////////////////////////////////////////
	// javascript helpers
	
	/**
	 * examples: * 
	 *   Ext::getCmp('name') rendered as: Ext.getCmp(<id of named widget>);
	 *   Ext::getCmp('name','Math.floor($0.axes.items[0].maximum)') rendered as:
	 *			Math.floor(Ext.getCmp(<id of named widget>).axes.items[0].maximum);
	 * 
	 * @param string   widget name
	 * @param string   operation
	 * @return string  javascript code
	 */
	public static function getCmp($name, $operation = null)
	{
		$buff = "Ext.getCmp('".Ext::w($name)->id."')";
		
		if ($operation !== null) {
			if (strpos($operation, "\$0") !== false) {
				$buff = str_replace("\$0", $buff, $operation);
			}
			else {
				$buff .= (".".$operation);
			}
		}
		
		return $buff;
	}
				
	public function getFunction($name)
	{
		if (array_key_exists($name, $this->_functionMap)) {
			return $this->_functions[$this->_functionMap[$name]];
		}
		else {
			return null;
		}
	}
	
	public function getVariable($name)
	{
		if (array_key_exists($name, $this->_variableMap)) {
			return $this->_variables[$this->_variableMap[$name]];
		}
		else {
			return null;
		}
	}
	
	public function getWidget($name)
	{
		$w = $this->getComponent($name);
		if ($w instanceof ExtWidget) {
			return $w;
		}
		else {
			throw new ExtException("Widget not found!");
		}
	}
	
	public function getClass($name)
	{
		if (array_key_exists($name, $this->_classMap)) {
			return $this->_classes[$this->_classMap[$name]];
		}
		else {
			return null;
		}
		
	}
	
	public function renderVariables($echo_result = true)
	{
		$result = "";
		foreach ($this->_variables as $var) {
			if (!$var->isRendered()) {
				$result .= $var->render($echo_result);
			}
		}
		if (!$echo_result) {
			return $result;
		}
	}

	public function renderFunctions($echo_result = true)
	{
		$result = "";
		foreach ($this->_functions as $fn) {
			if (!$fn->isRendered()) {
				$result .= $fn->render($echo_result);
			}
		}
		if (!$echo_result) {
			return $result;
		}		
	}
	
	public function renderClasses($echo_result = true)
	{
		$result = "";
		foreach ($this->_classes as $class) {
			if (!$class->isRendered()) {
				$result .= $class->render($echo_result);
			}
		}
		if (!$echo_result) {
			return $result;
		}
	}
	
	public function render($echo_result = true)
	{
		$result = "";
		$result .= $this->renderVariables($echo_result);
		$result .= $this->renderFunctions($echo_result);
		$result .= $this->renderClasses($echo_result);
		
		if (!$echo_result) {
			return $result;
		}
		else {
			return $this;
		}
	}
	
}