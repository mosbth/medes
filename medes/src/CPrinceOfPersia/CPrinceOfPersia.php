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

class CPrinceOfPersia {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected static $instance = null;
	public $config;
		
	
	// ------------------------------------------------------------------------------------
	//
	// Public internal variables
	//
	
	// medes installation related
	public $medesPath; // the root of the installation directory and adding medes
	public $installPath; // the root of the installation directory
	
	// site-related
	public $siteUrl;

	// current page-related
	protected static $currentUrl = null; // get this value though the method GetUrlToCurrentPage()

	// page-related
	public $pageTitle;
	public $pageKeywords;
	public $pageDescription;
	public $pageAuthor;
	public $pageCopyright;
	public $pageInlineStyle;
	
	// various
	public $googleAnalytics;
	public $navbar;
	
	
	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	protected function __construct() {
	
		// Full error reporting
		error_reporting(-1); 

		// set default exception handler
		set_exception_handler(array($this, 'DefaultExceptionHandler'));

		// Start a named session
		session_name(preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]));
		session_start();

		// path to medes installation directory
		$this->medesPath = realpath(dirname(__FILE__).'/../../');
		$this->installPath = realpath(dirname(__FILE__).'/../../../');

		// Get defaults from the configuration file
		$this->ReadConfigFromFile();

		// Set the siteurl from the stored configuration
		$this->siteUrl = $this->config['siteurl'];

		// Set default values to be empty
		$this->pageTitle='';
		$this->pageKeywords='';
		$this->pageDescription='';
		$this->pageAuthor='';
		$this->pageCopyright='';
		$this->pageInlineStyle='';
		$this->googleAnalytics='';


		$this->navbar = array(
			"1" => array("text"=>"home", "url"=>"medes/doc/home.php", "title"=>"Go home"),
			"2" => array("text"=>"showcase", "url"=>"medes/doc/showcase.php", "title"=>"See some live sites showing off"),
			"3" => array("text"=>"features", "url"=>"medes/doc/features.php", "title"=>"features"),
			"4" => array("text"=>"style", "url"=>"medes/doc/style.php", "title"=>"style"),
			"5" => array("text"=>"addons", "url"=>"medes/doc/addons.php", "title"=>"addons"),
			"6" => array("text"=>"download", "url"=>"medes/doc/download.php", "title"=>"download"),
			"7" => array("text"=>"contribute", "url"=>"medes/doc/contribute.php", "title"=>"contribute"),
			"8" => array("text"=>"docs", "url"=>"medes/doc/docs.php", "title"=>"docs"),
			"9" => array("text"=>"blog", "url"=>"medes/doc/blog.php", "title"=>"blog"),
			"10" => array("text"=>"about", "url"=>"medes/doc/about.php", "title"=>"about"),
			"11" => array("text"=>"adm", "url"=>"medes/adm.php", "title"=>"adm"),
		);


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
  	die("<p>File " . $aException->getFile() . " at line" . $aException->getLine() ."<br>Uncaught exception: " . $aException->getMessage());
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
		
		// treat all relative links as relative to sitelink, therefore prepens sitelink
		$nav = $this->config['navbar'];
		foreach($nav as $key => $val) {
			if(!(strstr('://', $nav[$key]['url']) || $nav[$key]['url'][0] == '/')) {
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
	// Set a link by adding the siteurl
	//  $aUrl: a link to a resource
	// 
	public function GetLinkToStylesheet() {
		return $this->PrependWithSiteUrl("medes/style/{$this->config['stylesheet']}");
	}


	// ------------------------------------------------------------------------------------
	//
	// Set a link by adding the siteurl
	//  $aUrl: a link to a resource
	// 
	public function PrependWithSiteUrl($aUrl) {
		
		$url = $aUrl;
		if($url[0] == '/') {
			$url = substr($url, 1, strlen($url)-1);
		}
		return $this->config['siteurl'] . $url;
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
			self::$currentUrl .= $_SERVER["SERVER_NAME"] . $serverPort . $_SERVER["REQUEST_URI"];
		}
		return self::$currentUrl;
	}


	// ------------------------------------------------------------------------------------
	//
	// Create an array of files in a directory. 
	// $aPath: a direct path to a directory
	//
	public static function ReadDirectory($aPath) {
		$ignore = array('.htaccess', '.git', '.svn');
		
		$list = Array();
		if(is_dir($aPath)) {
			if ($dh = opendir($aPath)) {
				while (($file = readdir($dh)) !== false) {
					if(is_file("$aPath/$file") && !in_array($file, $ignore)) {
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
	// Manages to gain or loose the admin access. 
	//
	public function GainOrLooseAdminAccess() {

		// Try to gain admin access
		if(isset($_GET['doGainAdminAccess'])) {
			$html = <<<EOD
<form action=? method=post>
	<fieldset class=standard>
		<legend>gain admin access</legend>
		<input type=password name=password>
		<input type=submit name=doCheckAdminPassword value=Login>
	</fieldset>
</form>
EOD;
			return $html;		
		}

		// Check the admin password and set admin access if correct
		if(isset($_POST['doCheckAdminPassword'])) {
			// check pwd 
			$_SESSION['hasAdminAccess'] = true;
		}

		// Loose admin access
		if(isset($_GET['doLooseAdminAccess'])) {
			$_SESSION['hasAdminAccess'] = false;			
		}

		// Does user already has admin access?
		if(isset($_SESSION['hasAdminAccess']) && $_SESSION['hasAdminAccess'] === true) {
			return "<p>You have admin access. <a href='?doLooseAdminAccess'>Loose it</a>.</p>";
		}
		return "<p>You need admin access. <a href='?doGainAdminAccess'>Get it</a>.</p>";		
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
		
		$className = get_class($this) ;
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
		
		$className = get_class($this) ;
		if(is_readable($this->medesPath . "/data/{$className}_config.php")) {
			$this->config = unserialize(file_get_contents($this->medesPath . "/data/{$className}_config.php"));
		} else {
			// data/config.php does not exists, redirect to installation procedure
		}	
	}
	
}