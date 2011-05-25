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
		$moduleExists 	= isset($this->cfg['config-db']['modules'][$controller]);
		$moduleEnabled 	= ($this->cfg['config-db']['modules'][$controller]['enabled'] == true);
		$class					= $this->cfg['config-db']['modules'][$controller]['class'];
		$classExists 		= class_exists($class);
		
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
	 * Render all views for a specific region
	 */
	public function RenderViewsForRegion($region) {
		// Sort views
		function cmp($a, $b) {
			if($a['prio'] == $b['prio']) {
				return 0;
			}
			return $a['prio'] > $b['prio'] ? 1 : -1;
		}
		usort($this->views[$region], 'cmp');

		// Render each view
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
  
		// Configuration array
		$v = new CView();
		$v->AddStatic('<hr><h2>$pp->cfg</h2><pre>' . print_r($this->cfg, true) . '</pre>');
		$this->AddView($v, 1);

		// Testing a view
		$v = new CView();
		$v->AddStatic('<hr><h2>$pp->req</h2><pre>' . print_r($this->req, true) . '</pre>');
		$this->AddView($v, 0);

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
		return;
		
		$pathToTheme = $this->PrependWithSiteUrl("medes/style/{$this->config['styletheme']['name']}");
		$stylesheet = isset($this->config['styletheme']['stylesheet']) ? "{$pathToTheme}/{$this->config['styletheme']['stylesheet']}" : "style/core/screen_compatibility.css";
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
	 * Get html for related sites menu
	 */
	public function GetHTMLForRelatedSitesMenu() {
		// treat all relative links as relative to sitelink, therefore prepend sitelink
		return "[relatedsitesmenu]";
		$nav = $this->config['navigation']['relatedsites']['nav'];
		return CNavigation::GenerateMenu($nav, false, '#mds-nav-relatedsites');		
  }


	/**
	 * Get html for login/logout/profile menu
	 */
	public function GetHTMLForLoginMenu() {
		return "[loginmenu]";
		$nav = array(
			"login"=>array("text"=>"login", "url"=>$this->PrependWithSiteUrl("medes/page/ucp.php?p=login"), "title"=>"Login"),
			"settings"=>array("text"=>"settings", "url"=>$this->PrependWithSiteUrl("medes/page/ucp.php"), "title"=>"Change your settings"),
			"acp"=>array("text"=>"acp", "url"=>$this->PrependWithSiteUrl("medes/page/acp.php"), "title"=>"Admin Control Panel"),
			"logout"=>array("text"=>"logout", "url"=>$this->PrependWithSiteUrl("medes/page/ucp.php?p=dologout"), "title"=>"Logout"),
		);

		if($this->uc->IsAuthenticated()) {
			unset($nav['login']);
			$nav['settings']['text'] = $this->uc->GetAccountName();
			if(!$this->uc->IsAdministrator()) {
				unset($nav['acp']);			
			}
		} else {
			unset($nav['settings']);
			unset($nav['acp']);
			unset($nav['logout']);			
		}

		return CNavigation::GenerateMenu($nav, false, '#mds-nav-login');		
  }


	/**
	 * Get html for navbar 
	 */
	public function GetHTMLForMainMenu() {
		//self::$menu[$p]['active'] = 'active';
		return "[mainmenu]";
		$cur = $this->req->GetUrlToCurrentPage();
		$nav = $this->config['navigation']['navbar']['nav'];
		foreach($nav as $key => $val) {
			if(!(strstr($nav[$key]['url'], '://') || $nav[$key]['url'][0] == '/')) {
				$nav[$key]['url'] = $this->PrependWithSiteUrl($nav[$key]['url']);
			}
			if(strpos($cur, $nav[$key]['url'])) {
				$nav[$key]['active'] = "active";
			}
		}		
		return CNavigation::GenerateMenu($nav, false, 'mds-nav-mainmenu');		
  }


	/**
	 * Get html for debug menu, usually used during development 
	 */
	public function GetHTMLForDeveloperMenu() {
		return "[developer menu]";
		$url = $this->req->GetUrlToCurrentPage();
		$nav1 = array(
			"phpmedes"	=>array("text"=>"phpmedes:", "class"=>"strong nolink"),			
			"site"	=>array("text"=>"phpmedes.org", "url"=>"http://phpmedes.org/", "title"=>"home of phpmedes project"),			
		);

		$nav2 = array(
			"tools"					=>array("text"=>"Tools:", "class"=>"strong nolink"),			
			"html5"					=>array("text"=>"html5", "url"=>"http://validator.w3.org/check/referer", "title"=>"html5 validator"),			
			"css3"					=>array("text"=>"css3", "url"=>"http://jigsaw.w3.org/css-validator/check/referer?profile=css3", "title"=>"css3 validator"),			
			"unicorn"				=>array("text"=>"unicorn", "url"=>"http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance", "title"=>"unicorn html and css validator"),			
			"cheatsheet"		=>array("text"=>"cheatsheet", "url"=>"http://www.w3.org/2009/cheatsheet/", "title"=>"html cheatsheet, lookup html-tags"),			
			"link-checker"	=>array("text"=>"link checker", "url"=>"http://validator.w3.org/checklink?uri=" . $url, "title"=>"css3 validator"),			
			"i18n-checker"	=>array("text"=>"i18n checker", "url"=>"http://qa-dev.w3.org/i18n-checker/index?async=false&amp;docAddr=" . $url, "title"=>"css3 validator"),			
			"check-header"	=>array("text"=>"check http-header", "url"=>"http://jigsaw.w3.org/css-validator/check/referer?profile=css3", "title"=>"css3 validator"),			
			"browsers"			=>array("text"=>"browsers", "url"=>"http://browsershots.org/{$url}", "title"=>"check browser compatibility"),	
			"colors"				=>array("text"=>"colors", "url"=>"http://www.workwithcolor.com/hsl-color-schemer-01.htm", "title"=>"color chooser"),	
		);

		$nav3 = array(
			"manuals"	=>array("text"=>"Manuals:", "class"=>"strong nolink"),			
			"html5"		=>array("text"=>"html5", "url"=>"http://dev.w3.org/html5/spec/spec.html", "title"=>"html5 specification"),			
			"css2"		=>array("text"=>"css2", "url"=>"http://www.w3.org/TR/CSS2/", "title"=>"css2 specification"),			
			"css3"		=>array("text"=>"css3", "url"=>"http://www.w3.org/Style/CSS/current-work#CSS3", "title"=>"css3 specification"),			
			"php"			=>array("text"=>"php", "url"=>"http://php.net/manual/en/index.php", "title"=>"php manual"),			
			"sqlite"	=>array("text"=>"sqlite", "url"=>"http://www.sqlite.org/lang.html", "title"=>"sqlite manual"),			
			"blueprint"	=>array("text"=>"blueprint", "url"=>"https://github.com/joshuaclayton/blueprint-css/wiki/Tutorials", "title"=>"blueprint tutorials on github"),			
		);

		$item1 = CNavigation::GenerateMenu($nav1, false);
		$item2 = CNavigation::GenerateMenu($nav2, false);
		$item3 = CNavigation::GenerateMenu($nav3, false);
		$time = round(microtime(true) - self::$timePageGeneration, 5);
		$numQueries = CDatabaseController::$numQueries;

		$reload= "";
		if(isset($_SESSION['timer'])) {
			$reload = "Page processed and redirected in {$_SESSION['timer']['time']} seconds with {$_SESSION['timer']['numQueries']} database queries.<br/>";
			unset($_SESSION['timer']);
		}

		$html = <<<EOD
<p class="clear"><em>{$reload}Page generated in {$time} seconds. There were {$numQueries} database queries.</em></p>
{$item1}{$item2}{$item3}
EOD;

		return $html;
  }


	// ------------------------------------ end of Template Engine related -------------------------




	//
	// Print the complete html-page 
	// $aContent: the html-code for the main content of the page
	// $aSidebar1: html-code for sidebar1 of the page, if null then do not use sidebar1
	// $aSidebar2: html-code for sidebar1 of the page, if null then do not use sidebar2
	//
	public function PrintHTMLPage($aContent=null, $aSidebar1=null, $aSidebar2=null) {
		if(!is_null($aContent)) $this->pageContent = $aContent;
		if(!is_null($aSidebar1)) $this->pageSidebar1 = $aSidebar1;
		if(!is_null($aSidebar2)) $this->pageSidebar2 = $aSidebar2;		
		
		if($this->pageSidebar1 && $this->pageSidebar2) {
			$this->classContent="span-16 border";
			$this->classSidebar1="span-4 border";
			$this->classSidebar2="span-4 last";			
		} else if($this->pageSidebar1) {
			$this->classContent="span-19 last";
			$this->classSidebar1="span-4 colborder";
		} else if($this->pageSidebar2) {
			$this->classContent="span-18 colborder";
			$this->classSidebar2="span-5 last";
		}

		$pp = &$this;
		
		if(!is_null($this->pageContentType)) {
			header("Content-Type: {$this->pageContentType}; charset={$this->pageCharset}");
		}

		ob_start();
		echo eval("?>" . $this->config['htmlparts-htmlhead']);

		echo is_null($this->pageTop) ? eval("?>" . $this->config['htmlparts-pagetop']) : $this->pageTop;
		echo is_null($this->pageHeader) ? eval("?>" . $this->config['htmlparts-pageheader']) : $this->pageHeader;		

		echo eval("?>" . $this->config['htmlparts-pagecontent']);
	
		echo is_null($this->pageFooter) ? eval("?>" . $this->config['htmlparts-pagefooter']) : $this->pageFooter;
		echo is_null($this->pageBottom) ? eval("?>" . $this->config['htmlparts-pagebottom']) : $this->pageBottom;
		echo "</body>\n</html>\n";
		ob_end_flush();
  }


	// ------------------------------------------------------------------------------------
	//
	// Set a link by adding the siteurl
	//  $aUrl: a link to a resource
	// 
	public function PrependWithSiteUrl($aUrl) {
		if(empty($aUrl)) {
			return false;
		}
			
		if(strpos($aUrl, '://') || $aUrl[0] == '/') {
			return $aUrl;
		}

		return $this->config['siteurl'] . $aUrl;
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


	// ------------------------------------------------------------------------------------
	//
	// Set the administrator password
	//  $aPwd: the password in plain text
	//  $aEncryptionFunction: a function that encrypts the password
	//
	public function SetAdminPassword($aPwd, $aEncryptionFunction='sha1') {
		
		$timestamp = md5(microtime());
		$this->config['password'] = array(
			'function'=>$aEncryptionFunction,
			'timestamp'=>$timestamp,
			'password'=>call_user_func($aEncryptionFunction, $timestamp.$aPwd.$timestamp),
		);
		$this->StoreConfigToFile();
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if password matches the administrator password
	//  $aPwd: the password in plain text
	//
	public function CheckAdminPassword($aPwd) {
		
		$password 	= $this->config['password']['password'];
		$function 	= $this->config['password']['function'];
		$timestamp 	= $this->config['password']['timestamp'];
		return $password == call_user_func($function, $timestamp.$aPwd.$timestamp);
	}


	// ------------------------------------------------------------------------------------
	// OBSOLETE, should be replaced by UpdateConfiguration()
	// Set the siteurl
	//  $aSiteUrl: string
	// 
	public function SetSiteUrl($aSiteUrl) {
		
		$this->config['siteurl'] = $aSiteUrl;
		$this->siteUrl = $aSiteUrl;
		$this->StoreConfigToFile();
	}


	// ------------------------------------------------------------------------------------
	//
	// Update configuration information and save it
	//  $aArray: an array with configuration values to save
	//  $aSave: true/false, should config be saved to file or not?
	//
	public function UpdateConfiguration($aArray, $aSave=true) {
		
		foreach($aArray as $key => $val) {
			$this->config[$key] = $val;
		}
		if($aSave) {
			$this->StoreConfigToFile();
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Store configuration settings to file
	//
	public function StoreConfigToFile() {
		
		$className = get_class($this);
		if(is_writable($this->medesPath . '/data/')) {
			file_put_contents($this->medesPath . "/data/{$className}_config.php", serialize($this->config));
		} else {
			throw new Exception('Failed to store CPrinceOfPercia configuration to file.');
		}	
	}


	// ------------------------------------------------------------------------------------
	//
	// Read configuration settings from file
	//
	public function ReadConfigFromFile() {
		
		$className = get_class($this);
		if(is_readable($this->medesPath . "/data/{$className}_config.php")) {
			$this->config = unserialize(file_get_contents($this->medesPath . "/data/{$className}_config.php"));
		} else {
			// data/config.php does not exists, redirect to installation procedure
		}	
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

