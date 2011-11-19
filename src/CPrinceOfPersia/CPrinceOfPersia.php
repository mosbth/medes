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
	

	/**
   * feedback, store feedback to user between page requests.
   * @var CRequest
   */
	public $feedback;
	

	/**
	 * Session name for storing feedback between page requests.
	 */
	const sessionNameFeedback = 'mds-feedback';
	
	
	/**
	 * A reference to the template file used by the template engine.
	 * 
	 * The theme defines a set of template files, these are set in the configuration array.
	 * Set this variable to define which template to use, if the key does not exists in the
	 * configuration array will the 'default' be used.
	 */
	public $template = 'default';
	
	
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
    $configFile = MEDES_SITE_PATH . '/config.php';
		if(is_file($configFile)) {
		  include($configFile);
		} else {
		  die(t('The file <code>site/config.php</code> is missing. <a href="setup.php">Run setup.php</a>.'));
		}

		// Start a named session
		session_name($this->cfg['session']['name']);
		session_start();

		// Set default date/time-zone
		date_default_timezone_set($this->cfg['server']['timezone']);
    
    // Set default character encoding to use for multibyte strings
		$this->pageCharset = $this->cfg['general']['character_encoding'];
    mb_internal_encoding($this->pageCharset);

		// Create the main database controller, where the Medes configuration is.
		extract($this->cfg['db'][0]);
		$this->db = new CDatabaseController($dsn, $username, $password, $driver_options);

		// Include general configuration from database.
		$cfg = $this->db->ExecuteSelectQueryAndFetchAll($this->SQL('load pp:config'));
		$this->cfg['config-db'] = unserialize($cfg[0]['value']);

		// Init global controllers and variables
		$this->uc = CUserController::GetInstance();
		$this->if = new CInterceptionFilter();

		// Init the requst object, fill with values from the current request
		$this->req = new CRequest();
		$this->req->Init($this->cfg['general']['base_url']);
		
		// Create and init the template engine
		//$this->te = new CTemplateEngine($this->cfg['config-db']['theme']);
		
		// A container for all views
		$this->views = array();

		// Init feedback
		$this->feedback = null;

    // These page specific items can be changed dynamically before rendering the page
		$this->pageDocType      = $this->cfg['config-db']['theme']['doctype']['doctype'];
		$this->pageContentType  = $this->cfg['config-db']['theme']['doctype']['contenttype'];
		$this->pageLang         = $this->cfg['config-db']['theme']['doctype']['lang'];
		$this->pageTitle        = null;
		$this->pageKeywords     = $this->cfg['config-db']['theme']['meta']['keywords'];
		$this->pageDescription  = $this->cfg['config-db']['theme']['meta']['description'];
		$this->pageAuthor       = $this->cfg['config-db']['theme']['meta']['author'];
		$this->pageCopyright    = $this->cfg['config-db']['theme']['meta']['copyright'];
    
    
		// TO BE REARRANGED	OBSOLETE?
		// Set default values to be empty
		//$this->pageFaviconLink='img/favicon.png';
		//$this->pageFaviconType='img/png';
		$this->pageStyle=null;
		$this->pageStyleLinks=array();
		$this->pageScript=null;
		$this->pageScriptLinks=array();
		$this->pageUseListForMenus=false;
		
		// TO BE REARRANGED	OBSOLETE?	
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
  public function FrontControllerRoute($aController = null, $aAction = null) {
		$controller 		= isset($aController) ? $aController : $this->req->controller;
		$action					= isset($aAction) ? $aAction : $this->req->action;
		$moduleExists 	= isset($this->cfg['config-db']['controllers'][$controller]);
		$moduleEnabled 	= false;
		$class					= false;
		$classExists 		= false;
		$canUrl 				= new CCanonicalUrl();

		if($moduleExists) {
			$moduleEnabled 	= ($this->cfg['config-db']['controllers'][$controller]['enabled'] == true);
			$class					= $this->cfg['config-db']['controllers'][$controller]['class'];
			$classExists 		= class_exists($class);
		}

    // Check if controller, action 
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
		} 
		// Check if canonical url exists
		else if(($url = $canUrl->CheckUrl($this->req->GetCanonicalUrl()))) {
      $this->req->ForwardTo($url);
      $this->FrontControllerRoute();
		} 
		// Page not found 404
		else { 
			$this->AddFeedbackError(t('Frontcontroller did not find a matching page.'));
			$this->AddFeedbackError("moduleExists=" . $moduleExists . ", moduleEnabled=" . $moduleEnabled . ", classExists=" . $classExists);
			$this->FrontControllerRoute('error', 'code404'); // internal redirect
		}
  
		// time after front controller
		$this->timer['front controller done'] = microtime(true); 
	}


	/**
	 * Check if we support clean urls.
	 */
	public function SupportCleanUrl() {
		return (isset($this->cfg['config-db']['general']['clean_url']) && $this->cfg['config-db']['general']['clean_url'] === false) ? false : true;
	}
	
	
	// ---------------------------------------------------------------------------------------------
	//
	// Template Engine and View, related stuff.
	// 
	
	/**
	 * Add view
	 */
	public function AddView($view, $prio=0, $region='content') {
	  static $count=0;
		$this->views[$region][] = array('view'=>$view, 'prio'=>$prio+$count++);
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
	  if(isset($this->views[$region]) && is_array($this->views[$region])) {
      usort($this->views[$region], 'CPrinceOfPersia::ViewCompare');
      foreach($this->views[$region] as $view) {
        $view['view']->Render();
      }
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
		
		// Create and print the header and codes, for example 200 404, etc
		;
		
		// Include template file, this hands over control to the theme to make callbacks to $pp.
		$pp = &$this;
		// Start by checking there is a template file.
		$realPath = $pp->cfg['config-db']['theme']['realpath'];
		$templates = $pp->cfg['config-db']['theme']['templates'];
		$tplFile = (isset($templates[$this->template])) ? $templates[$this->template] : $templates['default'];
		$tplFile = (substr($tplFile, 0, 1) == '/') ? $tplFile : "$realPath/$tplFile";
		if(is_file($tplFile)) {
		  // Is there a functions.php that comes with the theme?
			$tplFunctions = "$realPath/functions.php";
			if(is_file($tplFunctions)) {
				include $tplFunctions;
			}
			// Do the user have their own functions.php-files?
			if(isset($pp->cfg['config-db']['theme']['functions'])) {
			  foreach($pp->cfg['config-db']['theme']['functions'] as $file) {
          if(is_file($file)) {
            include $file;
          } else {
          	throw new Exception(t('#class error: Template site function file does not exist. File = @file', array('#class'=>get_class(), '@file'=>$file)));
          }
			  }
			}
			// Call the preprocess function for this request, if there is any.
			$func = 'hook_preprocess_' . str_replace('/', '_', $this->req->GetQueryPartOfUrl());
			if(function_exists($func)) {
			  call_user_func($func);
			}
			// Hand over to the template file thar renders the page.
			include $tplFile;
		} else {
			throw new Exception(t('#class error: Template file does not exist. File = @file', array('#class'=>get_class(), '@file'=>$tplFile)));			
		}

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
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$this->pageLang}" lang="{$this->pageLang}" class="{$this->template}">
EOD;
				break;
			
			case 'html5':
			default:
				$html = <<<EOD
<!DOCTYPE html>
<html lang="{$this->pageLang}" class="{$this->template}">
EOD;
				break;			
		}

		return $html;
	}


	/**
	 * Set the page template  
	 */
	public function SetTemplate($template) {
		$this->template = $template;
  }


	/**
	 * Set the page title 
	 */
	public function SetPageTitle($title) {
		$this->pageTitle = $title;
  }


	/**
	 * Get the page title 
	 */
	public function GetHTMLForPageTitle() {
    $title    = (isset($this->pageTitle)) ? $this->pageTitle : $this->cfg['config-db']['theme']['pagetitle']['default'];
    $prepend  = (isset($this->cfg['config-db']['theme']['pagetitle']['prepend'])) ? $this->cfg['config-db']['theme']['pagetitle']['prepend'] : null;
    return "<title>" . sanitizeHTML("{$prepend}{$title}") . "</title>";
  }


	/**
	 * Get html for header 
	 */
	public function GetHTMLForMeta() {
		$meta = "<meta charset='{$this->pageCharset}'/>\n";
		$meta .= is_null($this->pageKeywords)     ? null : "<meta name='keywords' content='{$this->pageKeywords}'/>\n";
		$meta .= is_null($this->pageDescription)  ? null : "<meta name='description' content='{$this->pageDescription}'/>\n";
		$meta .= is_null($this->pageAuthor)       ? null : "<meta name='author' content='{$this->pageAuthor}'/>\n";
		$meta .= is_null($this->pageCopyright)    ? null : "<meta name='copyright' content='{$this->pageCopyright}'/>\n";
		return $meta;
  }


	/**
	 * Get code for the favicon
	 */
	public function GetHTMLForFavicon() {
		$baseurl = $this->req->baseUrl . trim($this->cfg['config-db']['theme']['url'], '/');
		$favicon = trim($this->cfg['config-db']['theme']['favicon'], '/');
		return "<link rel='shortcut icon' href='{$baseurl}/{$favicon}'/>\n";
  }


	/**
	 * Create html to include stylesheets based on theme choosen in config
	 */ 
	public function GetHTMLForStyle() {
		$html = null;		
		$baseurl = $this->req->baseUrl . trim($this->cfg['config-db']['theme']['url'], '/');
		$stylesheets = $this->cfg['config-db']['theme']['stylesheets'];
		foreach($stylesheets as $style) {
		  if(!isset($style['enabled']) || $style['enabled']) {
			  $media = isset($style['media']) ? "media='{$style['media']}' " : null;
			  $type = isset($style['type']) ? "type='{$style['type']}' " : null;	
			  $html .= "<link rel='stylesheet' {$media}{$type}href='$baseurl/{$style['file']}'/>\n";
			}
		}
		if(!empty($this->pageStyle)) {
		  $html .= "<style>\n{$this->pageStyle}\n</style>";
		}
		return $html;
	}


	/** 
	 * Get html for script
	 */
	public function GetHTMLForScript() {
    if(!isset($this->cfg['config-db']['js'])) {
		  return;
		}
		
		$js = $this->cfg['config-db']['js'];
		$html = null;
    if(isset($js['external'])) {
      foreach($js['external'] as $val) {
  		  if(!isset($val['enabled']) || $val['enabled']) {
          $type = (isset($val['type'])) ? $val['type'] : "type='text/javascript'";
          $src  = (isset($val['src'])) ? $this->PrependWithSiteUrl($val['src']) : null;
          $html .= "<script {$type} src='{$src}'></script>\n";
        }
      }
    }
    
		$html .= isset($js['tracker']) ? "{$js['tracker']}\n" : "";
		$html .= isset($this->pageScript) ? "<script type='text/javascript'>\n{$this->pageStyle}\n</script>\n" : null;
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
						$menu['items'] = call_user_func($menu['callback'], $menu['items']);
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
	public static function ModifyLoginMenu($menu) {
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
		return $menu;
  }


	/**
	 * Callback function, check and set menu item to current
	 */
	public static function ModifyMenuDisplayCurrent($menu) {
		global $pp;
    $alt1 = $pp->req->controller;
    $alt2 = "{$alt1}/{$pp->req->action}";
    $alt3 = isset($pp->req->forwardedQuery) ? $pp->req->forwardedQuery : null;
		foreach($menu as $key=>$val) {
			if(($val['href'] == $alt1) || ($val['href'] == $alt2) || ($val['href'] == $alt3)) {
				$menu[$key]['active'] = true;
			}
		}
		return $menu;
  }


	/**
	 * Get html for logo 
	 * @return string
	 */
	public function GetHTMLForLogo() {
		$baseurl = $this->req->baseUrl . trim($this->cfg['config-db']['theme']['url'], '/');
		$src 	  = trim($this->cfg['config-db']['theme']['logo']['src'], '/');
		$alt 		= $this->cfg['config-db']['theme']['logo']['alt'];
		$width 	= $this->cfg['config-db']['theme']['logo']['width'];
		$height = $this->cfg['config-db']['theme']['logo']['height'];
		if(isset($this->cfg['config-db']['home'])) {
			$url 		= $this->cfg['config-db']['home']['href'];
			$title	= t($this->cfg['config-db']['home']['title']);		
		} else {
			$url 		= $this->req->CreateUrlToControllerAction('home');
			$title	= t('Home');
		}
		return "<a href='{$url}' title='{$title}'><img src='{$baseurl}/{$src}' alt='{$alt}' width='{$width}' height='{$height}'/></a>";
  }


	/**
	 * Get html for for short messages defined in cfg. 
	 * @return string
	 */
	public function GetHTMLMessage($aMessage) {
		if(isset($this->cfg['config-db']['theme']['messages'][$aMessage])) {
			return $this->cfg['config-db']['theme']['messages'][$aMessage];
	  } else {
			throw new Exception(t("Message '{$aMessage}' does not exist in config."));
		}
  }


	/**
	 * Get html for debug menu, usually used during development, disable using config.
	 */
	public function GetHTMLForDeveloper() {
	  if(!$this->cfg['config-db']['theme']['developer_tools']) {
	    return;
	  }
	  
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


	/**
	 * Add output as feedback to user. Stored in session.
	 */
	public function AddFeedback($feedback) {
		if(!isset($_SESSION[self::sessionNameFeedback])) {
			$_SESSION[self::sessionNameFeedback] = array();
		}
		$_SESSION[self::sessionNameFeedback][] = $feedback;
	}
	
	
	/**
	 * Get HTML for feedback.
	 *
	 * @return string The HTML for the feedback. 
	 */
	public function GetHTMLForFeedback() {
		$html = null;
		if(isset($_SESSION[self::sessionNameFeedback])) {		
			foreach($_SESSION[self::sessionNameFeedback] as $val) {
				$html .= "<p><output class='{$val['class']}'>{$val['message']}</output></p>\n";
			}
			unset($_SESSION[self::sessionNameFeedback]);
		}
		return $html;
	}


	/**
	 * Add feedback as success message.
	 */
	public function AddFeedbackSuccess($feedback) {
		$this->AddFeedback(array('class'=>'success', 'message'=>$feedback));
	}
	
	
	/**
	 * Add feedback as notice message.
	 */
	public function AddFeedbackNotice($feedback) {
		$this->AddFeedback(array('class'=>'notice', 'message'=>$feedback));
	}
	
	
	/**
	 * Add feedback as alert message.
	 */
	public function AddFeedbackAlert($feedback) {
		$this->AddFeedback(array('class'=>'alert', 'message'=>$feedback));
	}
	
	
	/**
	 * Add feedback as error message.
	 */
	public function AddFeedbackError($feedback) {
		$this->AddFeedback(array('class'=>'error', 'message'=>$feedback));
	}


	// ------------------------------------ end of Template Engine related -------------------------


	// ------------------------------------ IS BELOW OBSOLETE CODE? -------------------------


	/**
	 * SEEMS TO BE OBSOLETE
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
/*	public static function FormatDateTimeDiff($start, $startTimeZone=null, $end=null, $endTimeZone=null) {

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
/*
		// We use the two biggest parts
		if(count($format) > 1) {
				$format = array_shift($format)." and ".array_shift($format);
		} else {
				$format = array_pop($format);
		}

		// Prepend 'since ' or whatever you like
		return $interval->format($format);
	}
*/

	
}

