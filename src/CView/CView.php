<?php
/**
 * View, store information on a view and can render it.
 * @package MedesCore
 */
class CView {

  /**
   * Variable's to use when rendering view. Array with key and value. The key becomes a variable
   * name thats available in the included view file.
   * @var array
   */
	public $vars;

  /**
   * Path to file to include, the code in the file results in html, mapping html with vars.
   */
	public $path;

  /**
   * Fixed content of the view.
   */
	public $html;

  /**
   * Content to evaluate as PHP-code. Should contain <?php and ?>
   */
	public $php;

	
	/**
	 * Constructor
	 */
	public function __construct($def = null) {
		$this->vars = $this->path = $this->html = $this->php = null;
		if(is_array($def)) {
			$this->vars = isset($def['vars']) ? $def['vars'] : null;
			$this->path = isset($def['path']) ? $def['path'] : null;
			$this->html = isset($def['html']) ? $def['html'] : null;
			$this->php 	= isset($def['php'])  ? $def['php']  : null;
		} else if(is_string($def) && !empty($def)) {
			$this->html = $def;
		}
	}
	
	
	/**
	 * Magic method to alarm when setting member that does not exists. 
	 */
	public function __set($name, $value) {
		echo class_name() . ": Setting undefined member: {$name} => {$value}";
	}


	/**
	 * Add variables to the view
	 */
	public function AddVariables($vars) {
		$this->vars = array_merge($this->vars, $vars);	
	}
	
	
	/**
	 * Add static html to the view
	 */
	public function AddStatic($html) {
		$this->html .= $html;
	}
	
	
	/**
	 * Add html/php that should be parsed by eval.
	 */
	public function AddDynamic($php) {
		$this->php .= $php;
	}
	
	
	/**
	 * Add a file to include
	 */
	public function AddInclude($path) {
		$this->path = $path;
	}
	
	
	/**
	 * Render the view
	 */
	public function Render() {
		global $pp;
		
		if(isset($this->vars)) {
			extract($this->vars);
		}
		
		if(isset($this->html)) {
			echo $this->html;
		}

		if(isset($this->php)) {
			eval('?>' . $this->php);
		}

		if(isset($this->path)) {
			include($this->path);
		}
	}
	
	
} // End of class
