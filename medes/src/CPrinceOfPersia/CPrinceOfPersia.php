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
	private $config;
		
	
	// ------------------------------------------------------------------------------------
	//
	// Public internal variables
	//
	
	// medes installation related
	public $medesPath;
	
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
	
	// various
	public $googleAnalytics;
	
	
	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	protected function __construct() {
	
		// Full error reporting
		error_reporting(-1); 

		// Change to real sitelink when knowing how to extract it...
		$this->siteUrl = $_SERVER["SERVER_NAME"];

		// Start a named session
		session_name(preg_replace('/[:\.\/-_]/', '', $this->siteUrl));
		session_start();

		// path to medes installation directory
		$this->medesPath = realpath(dirname(__FILE__).'/../../');

		// Set default values to be empty
		$this->pageTitle='';
		$this->pageKeywords='';
		$this->pageDescription='';
		$this->pageAuthor='';
		$this->pageCopyright='';
		$this->googleAnalytics='';

		$this->ReadConfigFromFile();
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
		echo "<pre>"; 
    foreach($this as $key => $val) {
    	echo "$key = " . htmlentities($val) . "\n";
    }
    print_r($_SERVER);
    echo "</pre>";
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
	//
	public function SetAdminPassword($aPwd, $aFunction='md5') {
		
		$this->config['password'] = array('function'=>$aFunction, 'password'=>call_user_func($aEncryptionFunction, $aPwd));
		$this->StoreConfigToFile();
	}


	// ------------------------------------------------------------------------------------
	//
	// Store configuration settings to file
	//
	public function StoreConfigToFile() {
		
		if(is_writable($this->medesPath . '/data/config.php')) {
			file_put_contents($this->medesPath . '/data/config.php', serialize($this->config));
		} else {
			echo "Could";
		}	
		();
	}


	// ------------------------------------------------------------------------------------
	//
	// Read configuration settings from file
	//
	public function ReadConfigFromFile() {
		
		if(is_readable($this->medesPath . '/data/config.php')) {
			$this->config = unserialize(file_get_contents($this->medesPath . '/data/config.php'));
		} else {
			// data/config.php does not exists, redirect to installation procedure
		}	
	}
	
}