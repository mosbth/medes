<?php
/**
 * The Prince of Persia/Medes. One singleton object to rule them all.
 * 
 * @package MedesCore
 */
class CPrinceOfPersia implements ISingleton, IUsesSQL, IModule {

	/**#@+
	 * @access private
   */
	private static $instance = null;
	/**#@-*/
 
 
	/**#@+
	 * @access public
   */
	 
	/**
	 * Contains all config settings, defines, included from config.php or from database.
	 * @var array
   */
	public $cfg;
	
	/** 
	 * Timers, all timers stored in an array.
	 * @var array
   */
	public $timer;

	/**
	 * db, holding a database controller. Used to query the database.
   * @var CDatabaseController
   */
	public $db;
	
	/**
	 * uc, a reference to the user controller. Contains info about the current user.
   * @var CUserController
   */
	public $uc;
	
	/**
	 * if, a reference to the interception filter. Can be used to manage access to resources.
   * @var CInterceptionFilter
   */
	public $if;
	
	/**
	 * te, a reference to the template engine. A container for all views.
   * @var CTemplateEngine
   */
	//public $te;
	

	/**
	 * views, a container for all views.
   * @var array
   */
	public $views;
	

	/**
   * req, the current request.
   * @var CRequest
   */
	public $req;
	

	
	
	// CLEAN UP WITH PHPDOC LATER ON
	

	// site-related
	public $siteUrl;
	public $sessionName;

	// current page-related
	protected static $currentUrl = null; // get this value though the method GetUrlToCurrentPage()

	// page-related
	public $pageDocType;	
	public $pageContentType;	
	public $pageLang;	
	public $pageCharset;
	public $pageTitle;
	public $pageKeywords;
	public $pageDescription;
	public $pageAuthor;
	public $pageCopyright;
	public $pageFaviconLink;
	public $pageFaviconType;
	public $pageStyle;
	public $pageStyleLinks;
	public $pageScript;
	public $pageScriptLinks;
	public $pageUseListForMenus;
	
	// main content of page
	public $pageTop;	
	public $pageHeader;	
	public $pageContent;	
	public $pageSidebar1;	
	public $pageSidebar2;	
	public $pageFooter;	
	public $pageBottom;	
	public $classContent;	
	public $classSidebar1;	
	public $classSidebar2;	
	
	// various
	public $googleAnalytics;
	public $navbar;
 /**#@-*/
	
	
	/**
	 * Constructor
	 */
	protected function __construct() {

		// $pp should be an reference to the instance of this object	
		$pp = &$this;

		// time page generation
		$this->timer['first'] = microtime(true); 

		// set default exception handler
		set_exception_handler(array($this, 'DefaultExceptionHandler'));

		// include the site specific config.php
		include(MEDES_SITE_PATH . '/config.php');

		// Start a named session
		session_name($this->cfg['session']['name']);
		session_start();

		// Set default date/time-zone
		date_default_timezone_set($this->cfg['server']['timezone']);

		// Create the main database, where the Medes configuration is.
		extract($this->cfg['db'][0]);
		$this->db = new CDatabaseController($dsn, $username, $password, $driver_options);

		// Include general configuration from database.
		$cfg = $this->db->ExecuteSelectQueryAndFetchAll($this->SQL('load pp:config'));
		$this->cfg['config-db'] = unserialize($cfg[0]['value']);

		// Init global controllers and variables
		$this->uc = CUserController::GetInstance();
		$this->if = CInterceptionFilter::GetInstance();

		// Init the requst object, fill with values from the current request
		$this->req = new CRequest();
		$this->req->Init($this->cfg['general']['base_url']);
		
		// Create and init the template engine
		//$this->te = new CTemplateEngine($this->cfg['config-db']['theme']);
		
		// A container for all views
		$this->views = array();



		// TO BE REARRANGED		
		// Set default values to be empty
		$this->pageDocType='html5';
		$this->pageContentType='text/html';
		$this->pageLang='sv';
		$this->pageCharset='utf-8';
		$this->pageTitle=null;
		$this->pageKeywords=null;
		$this->pageDescription=null;
		$this->pageAuthor=null;
		$this->pageCopyright=null;
		$this->pageFaviconLink='img/favicon.png';
		$this->pageFaviconType='img/png';
		$this->pageStyle=null;
		$this->pageStyleLinks=array();
		$this->pageScript=null;
		$this->pageScriptLinks=array();
		$this->googleAnalytics=null;
		$this->pageUseListForMenus=false;
		
		// page content is default null
		$this->pageTop=null;
		$this->pageHeader=null;
		$this->pageContent=null;
		$this->pageSidebar1=null;
		$this->pageSidebar2=null;
		$this->pageFooter=null;
		$this->pageBottom=null;
		$this->classContent=null;
		$this->classSidebar1=null;
		$this->classSidebar2=null;

		// time after creation
		$this->timer['constructor done'] = microtime(true); 
	}
	
	
	/**
	 * Magic method to alarm when setting member that does not exists. 
	 */
	public function __set($name, $value) {
		echo get_class() . ": Setting undefined member: {$name} => {$value}";
	}

	
	/**
	 * Magic method to alarm when getting member that does not exists.
	 */
	public function __get($name) {
		throw new Exception(get_class() . ": Getting undefined member: {$name}");
	}

	/**
	 * Restart the session.
	 */
	public function DestroyAndRestartSession() {
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
    	$params = session_get_cookie_params();
    	setcookie(session_name(), '', time() - 42000,
      	$params["path"], $params["domain"],
      	$params["secure"], $params["httponly"]
      );
		}
		session_destroy();
		session_name($this->cfg['session']['name']);
		session_start();
		session_regenerate_id();		
	}


	/**
	 * Singleton pattern. Get the instance of the latest created object or create a new one. 
	 * @return CPrinceOfPersia The instance of this class.
	 */
	public static function GetInstance() {
		if(self::$instance == null) {
			self::$instance = new CPrinceOfPersia();
		}
		return self::$instance;
	}


	/**
	 * Create a common exception handler 
	 */
	public static function DefaultExceptionHandler($aException) {
		// CWatchdog to store logs
  	die("<h3>Exceptionhandler</h3><p>File " . $aException->getFile() . " at line" . $aException->getLine() ."<p>Uncaught exception: " . $aException->getMessage() . "<pre>" . print_r($aException->getTrace(), true) . "</pre>");
  }


	/**
 	 * Implementing interface IModule. Initiating when module is installed.
 	 */
	public function InstallModule() {
	}
	

	/**
 	 * Implementing interface IModule. Cleaning up when module is deinstalled.
 	 */
	public function DeinstallModule() {
	}
	

	/**
 	 * Implementing interface IModule. Called when updating to newer versions.
 	 */
	public function UpdateModule() {
	}
	
	
	/**
	 * Implementing interface IUsesSQL. Encapsulate all SQL used by this class.
	 */
  public static function SQL($id=null) {
  	$query = array(
  		'create table pp' => 'create table if not exists pp(module text, key text, value text, primary key(module, key))',
  		'load pp:config' => 'select value from pp where module="' . get_class() . '" and key="config"',
  		'save pp:config' => 'update pp set value=? where module="' . get_class() . '" and key="config"',
  	);
  
  	if(!isset($query[$id])) {
  		throw new Exception(t('#class error: Out of range. Query = @id', array('#class'=>get_class(), '@id'=>$id)));
		}
		
		return $query[$id];
	}	


	/**
	 * Frontcontroller, route to controllers.
	 */
  public function FrontControllerRoute() {
		$controller 		= $this->req->controller;
		$action					= $this->req->action;
		$moduleExists 	= isset($this->cfg['config-db']['controllers'][$controller]);
		$moduleEnabled 	= false;
		$class					= false;
		$classExists 		= false;

		if($moduleExists) {
			$moduleEnabled 	= ($this->cfg['config-db']['controllers'][$controller]['enabled'] == true);
			$class					= $this->cfg['config-db']['controllers'][$controller]['class'];
			$classExists 		= class_exists($class);
		}
		
		if($moduleExists && $moduleEnabled && $classExists) {
			$rc = new ReflectionClass($class);
			if($rc->implementsInterface('IController')) {
				if($rc->hasMethod($action)) {
					$controllerObj = $rc->newInstance();
					$method = $rc->getMethod($action);
					$method->invoke($controllerObj);
				} else {
					throw new Exception(t('#class error: Controller does not contain action.', array('#class'=>get_class())));		
				}
			} else {
				throw new Exception(t('#class error: Controller does not implement interface IController.', array('#class'=>get_class())));
			}
		} else {
			// 404
			$v = new CView();
			$v->AddStatic("<h2>404</h2>moduleExists=" . $moduleExists . ", moduleEnabled=" . $moduleEnabled . ", classExists=" . $classExists);
			$this->AddView($v);
		}
  
		// time after front controller
		$this->timer['front controller done'] = microtime(true); 
	}


	// ---------------------------------------------------------------------------------------------
	//
	// Template Engine and View, related stuff.
	// 
	
	/**
	 * Add view
	 */
	public function AddView($view, $prio=0, $region='content') {
		$this->views[$region][] = array('view'=>$view, 'prio'=>$prio);
	}
	
	
	/**
	 * Does the region contain any views?
	 */
	public function ViewExistsForRegion($region) {
		return isset($this->views[$region]);
	}
	
	
	/**
	 * Compare views when sorting array
	 */
	public static function ViewCompare($a, $b) {
		if($a['prio'] == $b['prio']) {
			return 0;
		}
		return $a['prio'] > $b['prio'] ? 1 : -1;
	}

	/**
	 * Render all views for a specific region
	 */
	public function RenderViewsForRegion($region) {
		usort($this->views[$region], 'CPrinceOfPersia::ViewCompare');
		foreach($this->views[$region] as $view) {
			$view['view']->Render();
		}
	}
	
	
	/**
	 * Template Engine Render, renders the views using the selected theme.
	 */
  public function TemplateEngineRender() {
  	// Get blocks from db?
  	// Panels?
  	// A block contains a CView? AddBlock()... CVIew has a title? Views rendered as block.
  	// theme regions can be divided into panels?
  	
  	// Menus?(is a CView in a block? AddMenu() or AddBlock
  	
  	// Create the theme template page and render onto it.
  
		// Timer before render all views onto template
		$this->timer['before-render'] = microtime(true); 
		
		// Include template file, this hands over control to the theme to make callbacks to $pp.
		$pp = &$this;
		$tplFile = $pp->cfg['config-db']['theme']['pathOnDisk'] . "/page.tpl.php";
		if(is_file($tplFile)) {
			$tplFunctions = $pp->cfg['config-db']['theme']['pathOnDisk'] . "/functions.php";
			if(is_file($tplFunctions)) {
				include $tplFunctions;
			}
			include $tplFile;
		} else {
			throw new Exception(t('#class error: Template file does not exist. File = @file', array('#class'=>get_class(), '@file'=>$tplFile)));			
		}
		//$this->te->Render();

		// last timer
		$this->timer['last'] = microtime(true); 
	}


	/**
	 * Create code for correct doctype
	 */ 
	public function GetHTMLDocType() {
		switch($this->pageDocType) {
			case 'xhtml-strict':
				$xml = "<?xml version='1.0' encoding='{$this->pageCharset}' ?>";
				$html = <<<EOD
{$xml}
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$this->pageLang}" lang="{$this->pageLang}">
EOD;
				break;
			
			case 'html5':
			default:
				$html = <<<EOD
<!DOCTYPE html>
<html lang="{$this->pageLang}">
EOD;
				break;			
		}

		return $html;
	}


	/**
	 * Get html for header 
	 */
	public function GetHTMLForMeta() {
		$meta = "<meta charset='{$this->pageCharset}'/>\n";
		$meta .= is_null($this->pageKeywords) ? '' : "<meta name='keywords' content='{$this->pageKeywords}'/>\n";
		$meta .= is_null($this->pageDescription) ? '' : "<meta name='description' content='{$this->pageDescription}'/>\n";
		$meta .= is_null($this->pageAuthor) ? '' : "<meta name='author' content='{$this->pageAuthor}'/>\n";
		$meta .= is_null($this->pageCopyright) ? '' : "<meta name='copyright' content='{$this->pageCopyright}'/>\n";
		return $meta;
  }


	/**
	 * Create html to include stylesheets based on theme choosen in config
	 */ 
	public function GetHTMLForStyle() {

		$html = null;		

		// get all stylesheets
		$baseurl = $this->req->baseUrl . trim($this->cfg['config-db']['theme']['url'], '/');
		$stylesheets = $this->cfg['config-db']['theme']['stylesheets'];
		foreach($stylesheets as $style) {
			$media = isset($style['media']) ? "media='{$style['media']}' " : null;
			$type = isset($style['type']) ? "type='{$style['type']}' " : null;	
			$html .= "<link rel='stylesheet' {$media}{$type}href='$baseurl/{$style['file']}'/>\n";
		}
		$faviconHref= $this->PrependWithSiteUrl($this->cfg['config-db']['theme']['favicon']);
		$html .= "<link rel='shortcut icon' href='{$faviconHref}'/>\n";

/*		
		isset($this->config['styletheme']['stylesheet']) ? "{$pathToTheme}/{$this->config['styletheme']['stylesheet']}" : "style/core/screen_compatibility.css";
		$print = isset($this->config['styletheme']['print']) ? "<link rel='stylesheet' media='print' type='text/css' href='{$pathToTheme}/{$this->config['styletheme']['print']}'/>\n" : "";
 		$ie = isset($this->config['styletheme']['ie']) ? "<!--[if IE]><link rel='stylesheet' media='screen, projection' type='text/css' href='{$pathToTheme}/{$this->config['styletheme']['ie']}'><![endif]-->\n" : "";
		$style = isset($this->pageStyle) ? "<style type='text/css'>{$this->pageStyle}</style>\n" : "";
		$favicon = empty($this->pageFaviconLink) ? null : $this->PrependWithSiteUrl($this->pageFaviconLink);
		$favicon = is_null($favicon) ? '' : "<link rel='shortcut icon' type='{$this->pageFaviconType}' href='{$favicon}'/>\n";

		$stylelinks='';
		foreach($this->pageStyleLinks as $val) {
			$media = isset($val['media']) ? "media='{$val['media']}'" : "media='all'";
			$type = isset($val['type']) ? "type='{$val['type']}'" : "type='text/css'";
			$href = "href='" . $this->PrependWithSiteUrl($val['href']) . "'";
			$stylelinks .= "<link rel='stylesheet' {$media} {$type} {$href}/>\n";
		}
		$html = <<<EOD
<link rel="stylesheet" media="all" type="text/css" href="{$stylesheet}"/>
{$print}
{$ie}
{$stylelinks}
{$style}
{$favicon}

EOD;
*/		

		return $html;
	}


	/** 
	 * Get html for script
	 */
	public function GetHTMLForScript() {
		return;
		$scriptlinks='';
		foreach($this->pageScriptLinks as $val) {
			$type = isset($val['type']) ? "type='{$val['type']}'" : "type='text/javascript'";
			$src = "src='" . $this->PrependWithSiteUrl($val['src']) . "'";
			$scriptlinks .= "<script {$type} {$src}></script>\n";
		}
		
		$script = isset($this->config['tracker']) ? "\n{$this->config['tracker']}\n" : "";
		$script .= isset($this->pageScript) ? "<script type='text/javascript'>\n{$this->pageStyle}\n</script>\n" : "";

		// Google analytics tracker code
		
		$html = <<<EOD
{$scriptlinks}
{$script}

EOD;

		return $html;		
  }


	/**
	 * Get html for menu
	 * @param string key corresponding to menu
	 * @return string or null if menu is not enabled.
	 */
	public function GetHTMLForMenu($aMenu) {
		if(isset($this->cfg['config-db']['menus'][$aMenu])) {
			$menu = $this->cfg['config-db']['menus'][$aMenu];
			if($menu['enabled']) {
				if(isset($menu['callback'])) {
					if(is_callable($menu['callback'])) {
						call_user_func($menu['callback'], &$menu['items']);
					} else {
						throw new Exception(t("Menu callback is not callable."));
					}
				}
				return CNavigation::GenerateMenu($menu['items'], $this->cfg['config-db']['menus']['list-style'], $menu['id'], $menu['class']);	
			}
		} else {
			throw new Exception(t('Menu "{$amenu}" does not exist in config.'));
		}
		return null;
  }


	/**
	 * Callback function, modify the items of the loginmenu
	 */
	public static function ModifyLoginMenu(&$menu) {
		global $pp;
		if($pp->uc->IsAuthenticated()) {
			unset($menu['login']);
			$menu['settings']['text'] = $pp->uc->GetUserAccount();
			if(!$pp->uc->IsAdministrator()) {
				unset($menu['acp']);			
			}
		} else {
			unset($menu['settings']);
			unset($menu['acp']);
			unset($menu['logout']);			
		}
  }

	/**
	 * Callback function, check and set menu item to current
	 */
	public static function ModifyMenuDisplayCurrent(&$menu) {
		global $pp;
		foreach($menu as $key=>$val) {
			$alt1 = $pp->req->controller;
			$alt2 = "{$alt1}/{$pp->req->action}";
			if(($val['href'] == $alt1) || ($val['href'] == $alt2)) {
				$menu[$key]['active'] = true;
			}
		}
  }


	/**
	 * Get html for logo 
	 * @return string
	 */
	public function GetHTMLForLogo() {
		$href 	= $this->PrependWithSiteUrl($this->cfg['config-db']['theme']['logo']['src']);
		$alt 		= $this->cfg['config-db']['theme']['logo']['alt'];
		$width 	= $this->cfg['config-db']['theme']['logo']['width'];
		$height = $this->cfg['config-db']['theme']['logo']['height'];
		$url		= $this->req->CreateUrlToControllerAction('home');
		$title	= t('Home');
		return "<a href='{$url}' title='{$title}'><img src='{$href}' alt='{$alt}' width='{$width}' height='{$height}'/>";
  }


	/**
	 * Get html for for short messages defined in cfg. 
	 * @return string
	 */
	public function GetHTMLMessage($aMessage) {
		if(isset($this->cfg['config-db']['messages'][$aMessage])) {
			return $this->cfg['config-db']['messages'][$aMessage];
		} else {
			throw new Exception(t("Message '{$aMessage}' does not exist in config."));
		}
  }


	/**
	 * Get html for debug menu, usually used during development 
	 */
	public function GetHTMLForDeveloper() {
		$url = $this->req->GetUrlToCurrentPage();
		$time = round(microtime(true) - $this->timer['first'], 5)*1000;
		$numQueries = $this->db->numQueries;

		$reload= "";
		if(isset($_SESSION['timer'])) {
			$reload = "Page processed and redirected in {$_SESSION['timer']['time']} msecs with {$_SESSION['timer']['numQueries']} database queries.<br/>";
			unset($_SESSION['timer']);
		}

		$html = <<<EOD
<p class="clear"><em>{$reload}Page generated in {$time} msecs. There were {$numQueries} database queries.</em></p>

<p>Tools: 
<a href="http://validator.w3.org/check/referer">html5</a>
<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">css3</a>
<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css21">css21</a>
<a href="http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance">unicorn</a>
<a href="http://validator.w3.org/checklink?uri={$url}">links</a>
<a href="http://qa-dev.w3.org/i18n-checker/index?async=false&amp;docAddr={$url}">i18n</a>
<!-- <a href="link?">http-header</a> -->
<a href="http://csslint.net/">css-lint</a>
<a href="http://jslint.com/">js-lint</a>
<a href="http://jsperf.com/">js-perf</a>
<a href="http://www.workwithcolor.com/hsl-color-schemer-01.htm">colors</a>
<a href="http://dbwebb.se/style">style</a>
</p>

<p>Docs:
<a href="http://www.w3.org/2009/cheatsheet">cheatsheet</a>
<a href="http://dev.w3.org/html5/spec/spec.html">html5</a>
<a href="http://www.w3.org/TR/CSS2">css2</a>
<a href="http://www.w3.org/Style/CSS/current-work#CSS3">css3</a>
<a href="http://php.net/manual/en/index.php">php</a>
<a href="http://www.sqlite.org/lang.html">sqlite</a>
<a href="http://www.blueprintcss.org/">blueprint</a>
</p>

EOD;

		return $html;
  }


	// ------------------------------------ end of Template Engine related -------------------------


	/**
	 * Set a link by adding the siteurl
	 * @param: $aUrl string a link to a resource
	 * @return: string
	 */
	public function PrependWithSiteUrl($aUrl) {
		$url = trim($aUrl, '/');
		
		if(empty($url)) {
			return null;
		}
			
		if(strpos($url, '://') || $url[0] == '/') {
			return $url;
		}

		return "{$this->req->baseUrl}{$url}";
	}


	// ------------------------------------------------------------------------------------
	//
	// Reload current page and save some information in the session. 
	// $aRemember: an array of values to rememer in the session
	//
	public static function ReloadPageAndRemember($aRemember=array(), $aPage=null) {
		// Store timing info before reloading
		$timer = array();
		$timer['time'] = round(microtime(true) - self::$timePageGeneration, 5);
		$timer['numQueries'] = CDatabaseController::$numQueries;
		$_SESSION['timer'] = $timer;
		
		// Save in session and reload page
		$_SESSION['remember'] = $aRemember;
		if(empty($aPage)) {
			header("Location: " . self::GetUrlToCurrentPage());
		} else {
			header("Location: " . $aPage);		
		}
		exit();
	}


	// ------------------------------------------------------------------------------------
	//
	// Get and clear the remebered information from the session. 
	// $aDefault: default values set in the remember array
	//
	public static function GetAndClearRememberFromSession($aDefault=array()) {
		$a = array();
		if(isset($_SESSION['remember'])) {
			$a = $_SESSION['remember'];
			unset($_SESSION['remember']);
		}
		foreach($aDefault as $key=>$val) {
			if(!isset($a[$key])) {
				$a[$key] = $aDefault[$key];
			}
		}
		return $a;
	}


	// ------------------------------------------------------------------------------------
	//
	// Create an array of files in a directory. 
	// $aPath: a direct path to a directory
	// $aTypes: what type of files to get
	//
	public static function ReadDirectory($aPath, $aTypes=array('file', 'dir')) {
		$ignore = array('.htaccess', '.git', '.svn', '..', '.');
		
		$list = Array();
		if(is_dir($aPath)) {
			if ($dh = opendir($aPath)) {
				while (($file = readdir($dh)) !== false) {
					if(in_array('file', $aTypes) && is_file("$aPath/$file") && !in_array($file, $ignore)) {
						$list[$file] = "$file"; 
					} elseif(in_array('dir', $aTypes) && is_dir("$aPath/$file") && !in_array($file, $ignore)) {
						$list[$file] = "$file";
					}
				}
				closedir($dh);
			}
		}
		sort($list, SORT_NUMERIC);
		return $list;
	}




	// ====================================================================================
	//
	//	Code below relates to the interface IDateTime
	//

	// ------------------------------------------------------------------------------------
	//
	// Needs PHP5.3
	// Copied from http://se.php.net/manual/en/dateinterval.format.php#96768
	// Modified (mos) to use timezones.
	//
	// A sweet interval formatting, will use the two biggest interval parts.
	// On small intervals, you get minutes and seconds.
	// On big intervals, you get months and days.
	// Only the two biggest parts are used.
	//
	// @param DateTime|string $start
	// @param DateTimeZone|string|null $startTimeZone
	// @param DateTime|string|null $end
	// @param DateTimeZone|string|null $endTimeZone
	// @return string
	//
	public static function FormatDateTimeDiff($start, $startTimeZone=null, $end=null, $endTimeZone=null) {

		if(!($start instanceof DateTime)) {
			if($startTimeZone instanceof DateTimeZone) {
				$start = new DateTime($start, $startTimeZone);
			} else if(is_null($startTimeZone)) {
				$start = new DateTime($start);
			} else {
				$start = new DateTime($start, new DateTimeZone($startTimeZone));
			}
		}

		if($end === null) {
				$end = new DateTime();
		}

		if(!($end instanceof DateTime)) {
			if($endTimeZone instanceof DateTimeZone) {
				$end = new DateTime($end, $endTimeZone);
			} else if(is_null($endTimeZone)) {
				$end = new DateTime($end);
			} else {
				$end = new DateTime($end, new DateTimeZone($endTimeZone));
			}
		}

		$interval = $end->diff($start);
		$doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals
		//$doPlural = create_function('$nb,$str', 'return $nb>1?$str."s":$str;'); // adds plurals

		$format = array();
		if($interval->y !== 0) {
			$format[] = "%y ".$doPlural($interval->y, "year");
		}
		if($interval->m !== 0) {
			$format[] = "%m ".$doPlural($interval->m, "month");
		}
		if($interval->d !== 0) {
			$format[] = "%d ".$doPlural($interval->d, "day");
		}
		if($interval->h !== 0) {
			$format[] = "%h ".$doPlural($interval->h, "hour");
		}
		if($interval->i !== 0) {
			$format[] = "%i ".$doPlural($interval->i, "minute");
		}
		if(!count($format)) {
				return "less than a minute";
		}
		if($interval->s !== 0) {
			$format[] = "%s ".$doPlural($interval->s, "second");
		}

/*
		if($interval->s !== 0) {
				if(!count($format)) {
						return "less than a minute";
				} else {
						$format[] = "%s ".$doPlural($interval->s, "second");
				}
		}
*/
		// We use the two biggest parts
		if(count($format) > 1) {
				$format = array_shift($format)." and ".array_shift($format);
		} else {
				$format = array_pop($format);
		}

		// Prepend 'since ' or whatever you like
		return $interval->format($format);
	}

}

