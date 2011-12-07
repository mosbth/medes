<?php
/**
 * Collects, processes and stores information on the current HTTP request.
 * 
 * @package MedesCore
 */
class CRequest {

	/**#@+
	 * @access private
   */
	/**#@-*/
 
 
	/**#@+
	 * @access public
   */
	public $supportCleanUrls;
	
	// below is current items of the request
	public $current;
	public $forwardedFrom;
	public $forwardedQuery;
	public $parts;
	public $script;
	public $dir;
	public $query;
	public $splits;
	public $baseUrl;
	public $baseUrlModified;
	public $baseSecureUrl;
	public $baseInsecureUrl;
	public $controller;
	public $action;
	public $params;
	public $args;
	public $get;
	public $post;
	public $session;
  	
	 
 /**#@-*/
	
	
	/**
	 * Constructor
	 */
	public function __construct($cleanUrls=false) {
		$this->current = null;
    $this->supportCleanUrls = $cleanUrls;
	}
	
	
	/**
	 * Magic method to alarm when setting member that does not exists. 
	 */
	public function __set($name, $value) {
		echo "Setting undefined member: {$name} => {$value}";
	}

	
	/**
	 * Magic method to alarm when getting member that does not exists.
	 * @return mixed
	 */
	public function __get($name) {
		echo "Getting undefined member: {$name}";
	}

	
	/**
	 * Init the requestobject and populate it with values from the actual request.
	 */
  public function Init($modifyBaseUrl = null) {
  	
  	// Step 1
  	// Take current url and divide in controller, action and parameters
  	$url 		= $this->GetUrlToCurrentPage();
  	$parts 	= parse_url($url);
  	$script	= $_SERVER['SCRIPT_NAME'];
 		$dir 		= rtrim(dirname($script), '/');
 		$query	= isset($modifyBaseUrl) && isset($_SERVER['REDIRECT_URL']) ? substr($_SERVER['REDIRECT_URL'], strlen($dir)) : substr($parts['path'], strlen($dir));
 		$splits = explode('/', trim($query, '/'));

		// If split is empty or equal to index.php, then use _GET['p'] to create controller/action,
		if(empty($splits[0]) || strcasecmp($splits[0], 'index.php') == 0) {
			if(isset($_GET['p'])) {
				$splits	= explode('/', trim($_GET['p'], '/'));		
			} else {
				$splits['0'] = 'index';
			}
		}

		// Step 2
		// Set controller, action and parameters
		$controller =  !empty($splits[0]) ? $splits[0] : 'index';
		$action 		=  !empty($splits[1]) ? $splits[1] : 'index';
		$args = $params = array();
		if(!empty($splits[2])) {
			$keys = $val = array();
			for($i=2, $cnt=count($splits); $i<$cnt; $i+=2) {
				$params[$splits[$i]] = !empty($splits[$i+1]) ? $splits[$i+1] : null;
				$args[] = $splits[$i];
				if(!empty($splits[$i+1])) {
  				$args[] = $splits[$i+1];
				}
			}
		}

		// Step 3
		// Store it
  	$this->current	= $url;
  	$this->forwardedFrom = null;
  	$this->parts		= $parts;
  	$this->script		= $script;
  	$this->dir 			= $dir;
  	$this->query 		= $query;
  	$this->splits 	= $splits;
  	$this->baseUrl 	= rtrim("{$parts['scheme']}://{$parts['host']}" . (isset($parts['port']) ? ":{$parts['port']}" : '') . "{$dir}", '/') . '/';
  	$this->baseUrlModified = isset($modifyBaseUrl) ? rtrim($modifyBaseUrl, '/') . '/' : null;
  	$this->baseSecureUrl 		= str_replace('http://', 'https://', $this->baseUrl);
  	$this->baseInsecureUrl 	= str_replace('https://', 'http://', $this->baseUrl);
  	$this->controller = $controller;
  	$this->action 		= $action;
  	$this->params 		= $params;
  	$this->args 			= $args;
  	$this->get 			= &$_GET;
  	$this->post 		= &$_POST;
  	$this->session 	= &$_SESSION;
	}


	/**
	 * Forward a request from current (canonical) url to another internal (not so canonical) url.
	 *
	 * Changes some parameters in the request and adds forwarded.
	 *
	 */
  public function ForwardTo($to) {
  	if(strpos($to, '?')) {
	  	$to = substr($to, 0, strpos($to, '?'));
			if($to[strlen($to)] != '/') {
				$to = dirname($to);
			}
		}

  	$parts 	= parse_url($this->current);
  	$script	= $_SERVER['SCRIPT_NAME'];
 		$dir 		= rtrim(dirname($script), '/');
 		$query	= trim(substr($parts['path'], strlen($dir)), '/');

  	$to = trim($to, '/');
  	$this->forwardedFrom 	= $this->current;
  	$this->forwardedQuery	= $query;
  	$this->current				= $this->baseUrl . $to;
  	$this->parts					= parse_url($this->current);
  	//$this->script		= $_SERVER['SCRIPT_NAME'];
  	$this->script = null;
  	//$this->dir 			= rtrim(dirname($this->script), '\/');
  	$this->dir = null;
  	//$this->query 		= substr($parts['path'], strlen($dir));
  	$this->query = $to;
  	$this->splits = $splits = explode('/', $to);

		// If split is empty or equal to index.php, then use _GET['p'] to create controller/action,
		if(empty($splits[0]) || strcasecmp($splits[0], 'index.php') == 0) {
			if(isset($_GET['p'])) {
				$splits	= explode('/', trim($_GET['p'], '/'));		
			} else {
				$splits['0'] = 'index';
			}
		}

		// Set up controller and action from $to
		$controller =  !empty($splits[0]) ? $splits[0] : 'index';
		$action 		=  !empty($splits[1]) ? $splits[1] : 'index';
		$args = $params = array();
		if(!empty($splits[2])) {
			$keys = $val = array();
			for($i=2, $cnt=count($splits); $i<$cnt; $i+=2) {
				$params[$splits[$i]] = !empty($splits[$i+1]) ? $splits[$i+1] : null;
			}
			$args = array_merge(array_keys($params), array_values($params));
		}

  	$this->controller = $controller;
  	$this->action 		= $action;
  	$this->params 		= $params;
  	$this->args 			= $args;
	}
	

	/**
	 * Get query part of url, except the querystring started by ? 
	 */
	public function GetQueryPartOfUrl() {
		return trim($this->query, '/');
	}


	/**
	 * Get url part that gives controller and action with parameters.
	 */
	public function GetCanonicalUrl() {
	  if(isset($this->get['p'])) {
	    $p = trim($this->get['p'], '/');
	    if(!empty($p)) {
	      return trim($p, '/');
	    }
	  }
		return trim($this->query, '/');
	}


	/**
	 * Get the url to the current page. 
	 */
	public function GetUrlToCurrentPage() {
		if(isset($this->forwardedFrom)) {
			return $this->forwardedFrom;
		} else if(!isset($this->current)) {
			$url = "http";
			$url .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
			$url .= "://";
			$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
			(($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
			$url .= $_SERVER["SERVER_NAME"] . $serverPort . htmlspecialchars($_SERVER["REQUEST_URI"]);
			$this->current = $url;
		}
		return $this->current;
	}


	/**
	 * Redirect to another page.
	 *
	 * Can be called with variable amount of arguments.
	 * 
	 * @param string $controller
	 * @param string $action
	 * @param array $params array with values to be combined in url
	 */
  public function RedirectTo($aController = null, $aAction = null, $aParams = null) {
		$url = $this->CreateUrlToControllerAction($aController, $aAction, $aParams);
		$params = null;
		$num = func_num_args();
		if($num > 2) {
			for($i=2; $i < $num; $i++) {
				$params .= '/' . func_get_arg($i);
			}
		}
  	header("Location: $url$params");
  	exit;
	}
		

	/**
	 * Create url to page using current settings.
	 *
	 * Can be called with variable amount of arguments.
	 * 
	 * @param string $controller
	 * @param string $action
	 * @param string $args this function takes a flexible number of arguments to be combined in url
	 */
	public function CreateUrlToControllerAction($controller = null, $action = null /*, $args=null */) {
		$controller = isset($controller) ? $controller : $this->controller;
		$action = isset($action) ? "/$action" : null;
		$args = null;
		$num = func_num_args();
		if($num > 2) {
			for($i=2; $i < $num; $i++) {
				$args .= '/' . func_get_arg($i);
			}
		}
		return $this->CleanUrl("$controller$action$args");
	}
	

	/**
	 * Enable support of clean urls or through querystring, depending on configuration.
	 *
	 * This is to support those sites where mod_rewrite (or equal) to clean urls does not work.
	 * 
	 * @param string $url
	 */
	public function CleanUrl($url) {
		$base = isset($this->baseUrlModified) ? $this->baseUrlModified : $this->baseUrl;
		return ($this->supportCleanUrls) ? "$base$url" : "$base?p=$url";
	}
	

	/**
	 * Create an absolute url from the argument which may be absolute or relative.
	 *
	 * @param string $url
	 */
	public function CheckUrl($url) {
		if(strpos($url, '://') || $url[0] == '/') {
			return $url;
		}

		$url = trim($url, '/');
		if(empty($url)) {
			return null;
		}
		
		return $this->CleanUrl($url);
	}


/* OBSOLETE?
	// ------------------------------------------------------------------------------------
	//
	// Modify query string of the url.
	//
	// array $queryString: array to merge with current querystring, overwrites if there is
	//               duplicate keys.
	// string $aUrl: if null, or omitted, get currentUrl from GetUrlToCurrentPage().
	//
	// returns string: the url with the updated query string.
	//
	public static function ModifyQueryStringOfCurrentUrl(array $aQueryString, string $aUrl=null) {
		$url = is_null($aUrl) ? self::GetUrlToCurrentPage() : $aUrl;
		$parts = parse_url($url);
		parse_str($parts['query'], $qs);
		$qs = array_merge($qs, $aQueryString);
		if(empty($qs)) {
			unset($parts['query']);
		} else {
			$parts['query'] = http_build_query($qs);
		}
		return self::BuildUrl($parts);
	}


	// ------------------------------------------------------------------------------------
	//
	// Build an url from array produced by parse_url(). Does not support user & pass.
	//
	// array $aParts: the parts of the url, in the form as produced by parse_url().
	//
	// returns string: the resulting url.
	//
	public static function BuildUrl(array $aParts) {
		$port = isset($aParts['port']) ? ":{$aParts['port']}" : "";
		$query = isset($aParts['query']) ? "?{$aParts['query']}" : "";
		$fragment = isset($aParts['fragment']) ? "#{$aParts['fragment']}" : "";		
		return "{$aParts['scheme']}://{$aParts['host']}{$port}{$aParts['path']}{$query}{$fragment}";
	}


/* Needed?
	// ------------------------------------------------------------------------------------
	//
	// Get query string as string.
	//
	public static function GetQueryString() {
		$qs = Array();
		parse_str($_SERVER['QUERY_STRING'], $qs);
		return (empty($qs) ? '' : htmlspecialchars(http_build_query($qs)));
	}
*/

/*
	// ------------------------------------------------------------------------------------
	//
	// Static function
	// Parse query string and add items to it. Return the modified query string.
	// array $aQueryStr: items to add to query string, key and values
	//
	public static function QueryStringAddItems(array $items=array()) {
		$qs = Array();
		parse_str($_SERVER['QUERY_STRING'], $qs);
		$qs = array_merge($qs, $items);
		return (empty($qs) ? '' : htmlspecialchars(http_build_query($qs)));
	}
*/

} // End of class
