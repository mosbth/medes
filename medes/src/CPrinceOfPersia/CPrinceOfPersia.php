<?php
// ===========================================================================================
//
// File: CPrinceOfPersia.php
//
// Description: The master of phpmedes. Controls configuration and setup. 
// It does a lot of things, just like a real Prince should do.
//
// Author: Mikael Roos
//
// History:
// 2010-10-21: Created
//

interface ISingleton {
	// ------------------------------------------------------------------------------------
	//
	// Singleton pattern.
	// Get the instance of the latest created object or create a new one. 
	//
	public static function GetInstance();
	
}

interface IFrontController {

	// ------------------------------------------------------------------------------------
	//
	// Frontcontroller. Redirect to choosen page and return the resulting html. 
	//
	public static function DoIt();
	
}

interface IActionHandler {

	// ------------------------------------------------------------------------------------
	//
	// Manage _GET and _POST requests and redirect or return the resulting html. 
	//
	public function ActionHandler();
	
}

interface IDatabaseObject {

	// ------------------------------------------------------------------------------------
	//
	// Get SQL that this object support. 
	//
  public static function GetSQL($which);

	// ------------------------------------------------------------------------------------
	//
	// Insert new object to database. 
	//
	public function Insert();
	
	// ------------------------------------------------------------------------------------
	//
	// Update existing object in database. 
	//
	public function Update();
	
	// ------------------------------------------------------------------------------------
	//
	// Save object to database. Manage if insert or update.
	//
	public function Save();
	
	// ------------------------------------------------------------------------------------
	//
	// Load object from database. 
	//
	public function Load();
	
	// ------------------------------------------------------------------------------------
	//
	// Delete object from database. 
	// $really: Put object in wastebasket (false) or really delete row from table (true)
	//
	public function Delete($really=false);
	
}

interface IInstallable {
	// ------------------------------------------------------------------------------------
	//
	//  Installation routine for this class
	//
	public function Install();
	
}

interface IDateTime {
	// ------------------------------------------------------------------------------------
	//
	//  Format a date and time, display the intervall between two dates using the largest 
	//	diff-part.
	//
	public static function FormatDateTimeDiff($start, $end=null);
	
}

interface ILanguage {
	// ------------------------------------------------------------------------------------
	//
	// Gather all language-strings behind one method. 
	// Store all strings in self::$lang.
	//
	public static function InitLanguage($language=null);
	
}

interface IAddOn {
	// ------------------------------------------------------------------------------------
	//
	//  
	//
}



class CPrinceOfPersia implements iSingleton, IDateTime {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected static $instance = null;
	public $config;
	public static $timePageGeneration = 0;

	// CUserController
	public $uc;
	
	// ------------------------------------------------------------------------------------
	//
	// Public internal variables
	//
	
	// medes installation related
	public $medesPath; // the root of the installation directory and adding medes
	public $installPath; // the root of the installation directory
	
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
	
	// various
	public $googleAnalytics;
	public $navbar;
	
	
	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	protected function __construct() {
	
		// time page generation
		self::$timePageGeneration = microtime(true); 

		// Full error reporting
		error_reporting(-1); 

		// set default exception handler
		set_exception_handler(array($this, 'DefaultExceptionHandler'));

		// Start a named session
		$this->sessionName = $_SERVER["SERVER_NAME"];
		session_name(preg_replace('/[:\.\/-_]/', '', $this->sessionName));
		session_start();

		// Set default date/time-zone
		date_default_timezone_set('Europe/Stockholm');

		// path to medes installation directory
		$this->medesPath = realpath(dirname(__FILE__).'/../../');
		$this->installPath = realpath(dirname(__FILE__).'/../../../');

		// Get defaults from the configuration file
		$this->ReadConfigFromFile();

		// Set the siteurl from the stored configuration
		$this->siteUrl = $this->config['siteurl'];

		// Get hold of the controllers, just in case
		$this->uc = CUserController::GetInstance();

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
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Singleton pattern.
	// Get the instance of the latest created object or create a new one. 
	//
	public static function GetInstance() {
	
		if(self::$instance == NULL) {
			self::$instance = new CPrinceOfPersia();
		}
		return self::$instance;
	}


	// ------------------------------------------------------------------------------------
	//
	// Dump current settings. 
	//
	public function Dump() {
		$html = "<pre>"; 
    foreach($this as $key => $val) {
   		if(is_array($val)) {
    		$html .= "$key = " . htmlentities(print_r($val, true)) . "\n";
    	} else {
    		$html .= "$key = " . htmlentities($val) . "\n";
    	}
    }
    $html .= "</pre>";
    
    return $html;
	}


	// ------------------------------------------------------------------------------------
	//
	// Create a common exception handler 
	//
	public static function DefaultExceptionHandler($aException) {
  	die("<p>File " . $aException->getFile() . " at line" . $aException->getLine() ."<p>Uncaught exception: " . $aException->getMessage() . "<pre>" . print_r($aException->getTrace(), true) . "</pre>");
  }


	// ------------------------------------------------------------------------------------
	//
	// Create code for correct doctype
	// 
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


	// ------------------------------------------------------------------------------------
	//
	// Create html to include stylesheets based on theme choosen in config
	// 
	public function GetHTMLForStyle() {
		
		$pathToTheme = $this->PrependWithSiteUrl("medes/style/{$this->config['styletheme']['name']}");
		$stylesheet = "{$pathToTheme}/{$this->config['styletheme']['stylesheet']}";
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


	// ------------------------------------------------------------------------------------
	//
	// Get html for script
	//
	public function GetHTMLForScript() {
		$scriptlinks='';
		foreach($this->pageScriptLinks as $val) {
			$type = isset($val['type']) ? "type='{$val['type']}'" : "type='text/javascript'";
			$src = "src='" . $this->PrependWithSiteUrl($val['src']) . "'";
			$scriptlinks .= "<script {$type} {$src}></script>\n";
		}
		
		$script = isset($this->pageScript) ? "<script type='text/javascript'>\n{$this->pageStyle}\n</script>\n" : "";

		$html = <<<EOD
{$scriptlinks}
{$script}

EOD;

		return $html;		
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for related sites menu
	//
	public function GetHTMLForRelatedSitesMenu() {
		// treat all relative links as relative to sitelink, therefore prepend sitelink
		$nav = $this->config['navigation']['relatedsites']['nav'];
		return CNavigation::GenerateMenu($nav, false, 'relatedsites');		
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for login/logout/profile menu
	//
	public function GetHTMLForProfileMenu() {
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

		return CNavigation::GenerateMenu($nav, false, 'profile');		
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for header 
	//
	public function GetHTMLForMeta() {
		$meta = "<meta charset='{$this->pageCharset}'/>\n";
		$meta .= is_null($this->pageKeywords) ? '' : "<meta name='keywords' content='{$this->pageKeywords}'/>\n";
		$meta .= is_null($this->pageDescription) ? '' : "<meta name='description' content='{$this->pageDescription}'/>\n";
		$meta .= is_null($this->pageAuthor) ? '' : "<meta name='author' content='{$this->pageAuthor}'/>\n";
		$meta .= is_null($this->pageCopyright) ? '' : "<meta name='copyright' content='{$this->pageCopyright}'/>\n";
		return $meta;
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for header 
	//
	public function GetHTMLForHeader() {
		//$GLOBALS['GetHTMLForHeader'] = $this->config['header'];
		//include "var://GetHTMLForHeader";
		//return $this->config['header'];
		return eval("?>" . $this->config['header']);
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for navbar 
	//
	public function GetHTMLForNavbar() {
		//self::$menu[$p]['active'] = 'active';
		$nav = $this->config['navigation']['navbar']['nav'];
		foreach($nav as $key => $val) {
			if(!(strstr($nav[$key]['url'], '://') || $nav[$key]['url'][0] == '/')) {
				$nav[$key]['url'] = $this->PrependWithSiteUrl($nav[$key]['url']);
			}
		}		
		return CNavigation::GenerateMenu($nav, false, 'mainmenu');		
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for footer 
	//
	public function GetHTMLForFooter() {
		return eval("?>" . $this->config['footer']);
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for debug menu, usually used during development 
	//
	public function GetHTMLForDeveloperMenu() {
		$url = $this->GetUrlToCurrentPage();
		$nav1 = array(
			"phpmedes"	=>array("text"=>"PhpMedes", "class"=>"nav-h1 nolink"),			
			"site"	=>array("text"=>"phpmedes.org", "url"=>"http://phpmedes.org/", "title"=>"home of phpmedes project"),			
		);

		$nav2 = array(
			"tools"					=>array("text"=>"Tools", "class"=>"nav-h1 nolink"),			
			"html5"					=>array("text"=>"html5", "url"=>"http://validator.w3.org/check/referer", "title"=>"html5 validator"),			
			"css3"					=>array("text"=>"css3", "url"=>"http://jigsaw.w3.org/css-validator/check/referer?profile=css3", "title"=>"css3 validator"),			
			"unicorn"				=>array("text"=>"unicorn", "url"=>"http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance", "title"=>"unicorn html and css validator"),			
			"cheatsheet"		=>array("text"=>"cheatsheet", "url"=>"http://www.w3.org/2009/cheatsheet/", "title"=>"html cheatsheet, lookup html-tags"),			
			"link-checker"	=>array("text"=>"link checker", "url"=>"http://validator.w3.org/checklink?uri=" . $url, "title"=>"css3 validator"),			
			"i18n-checker"	=>array("text"=>"i18n checker", "url"=>"http://qa-dev.w3.org/i18n-checker/index?async=false&amp;docAddr=" . $url, "title"=>"css3 validator"),			
			"check-header"	=>array("text"=>"check http-header", "url"=>"http://jigsaw.w3.org/css-validator/check/referer?profile=css3", "title"=>"css3 validator"),			
			"browsers"			=>array("text"=>"browsers", "url"=>"http://browsershots.org/{$url}", "title"=>"check browser compatibility"),	
		);

		$nav3 = array(
			"manuals"	=>array("text"=>"Manuals", "class"=>"nav-h1 nolink"),			
			"html5"		=>array("text"=>"html5", "url"=>"http://dev.w3.org/html5/spec/spec.html", "title"=>"html5 specification"),			
			"css2"		=>array("text"=>"css2", "url"=>"http://www.w3.org/TR/CSS2/", "title"=>"css2 specification"),			
			"css3"		=>array("text"=>"css3", "url"=>"http://www.w3.org/Style/CSS/current-work#CSS3", "title"=>"css3 specification"),			
			"php"			=>array("text"=>"php", "url"=>"http://php.net/manual/en/index.php", "title"=>"php manual"),			
			"sqlite"	=>array("text"=>"sqlite", "url"=>"http://www.sqlite.org/lang.html", "title"=>"sqlite manual"),			
			"blueprint"	=>array("text"=>"blueprint", "url"=>"https://github.com/joshuaclayton/blueprint-css/wiki/Tutorials", "title"=>"blueprint tutorials on github"),			
		);

		$item1 = CNavigation::GenerateMenu($nav1, false, "span-3");
		$item2 = CNavigation::GenerateMenu($nav2, false, "span-3");
		$item3 = CNavigation::GenerateMenu($nav3, false, "span-3 last");
		$time = round(microtime(true) - self::$timePageGeneration, 5);
		$numQueries = CDatabaseController::$numQueries;

		$reload= "";
		if(isset($_SESSION['timer'])) {
			$reload = "Page reloaded in {$_SESSION['timer']['time']} seconds with {$_SESSION['timer']['numQueries']} database queries.<br/>";
			unset($_SESSION['timer']);
		}

		$html = <<<EOD
<p class="clear"><em>{$reload}Page generated in {$time} seconds. There were {$numQueries} database queries.</em></p>
{$item1}{$item2}{$item3}
EOD;

		return $html;
  }


	// ------------------------------------------------------------------------------------
	//
	// Print the complete html-page 
	// $aPage: the html-code for the page
	// $aHeader: html-code for the header of the page, if empty using default
	// $aFooter: html-code for the footer of the page, if empty using default
	//
	public function PrintHTMLPage($aPage="", $aHeader="", $aFooter="") {
		$pp = &$this;
		if(!is_null($this->pageContentType)) {
			header("Content-Type: {$this->pageContentType}; charset={$this->pageCharset}");
		}
		include(dirname(__FILE__) . "/htmlheader.php");
		echo empty($aHeader) ? $this->GetHTMLForHeader() : $aHeader;
		echo $aPage;
		echo empty($aFooter) ? $this->GetHTMLForFooter() : $aFooter;
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
	// Get the link to the current page. 
	//
	public static function GetUrlToCurrentPage() {
		if(!self::$currentUrl) {
			self::$currentUrl = "http";
			self::$currentUrl .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
			self::$currentUrl .= "://";
			$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
			(($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
			self::$currentUrl .= $_SERVER["SERVER_NAME"] . $serverPort . htmlspecialchars($_SERVER["REQUEST_URI"]);
		}
		return self::$currentUrl;
	}


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
	//
	public function UpdateConfiguration($aArray) {
		
		foreach($aArray as $key => $val) {
			$this->config[$key] = $val;
		}
		$this->StoreConfigToFile();
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

