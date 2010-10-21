<?php
// ===========================================================================================
//
// File: CConfigSite.php
//
// Description: A global config-object containing values and method used all over the site.
//
// Author: Mikael Roos
//
// History:
// 2010-10-21: Created
//

class CConfigSite {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
	protected static $iInstance = NULL;
		
	
	// ------------------------------------------------------------------------------------
	//
	// Public internal variables
	//
	
	// medes installation related
	public $medesPath;
	
	// site-related
	public $siteUrl;

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
		$this->medesPath = realpath(dirname(__FILE__).'/../');

		// Set default values to be empty
		$this->pageTitle='';
		$this->pageKeywords='';
		$this->pageDescription='';
		$this->pageAuthor='';
		$this->pageCopyright='';
		$this->googleAnalytics='';

		// Replace this to methods in this object
		include("functions.php");
	
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Get the instance of the latest created object or create a new one. 
	// Singleton pattern.
	//
	public static function GetInstance() {
	
		if(self::$iInstance == NULL) {
			self::$iInstance = new CConfigSite();
		}
		return self::$iInstance;
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
	public function GetLinkToCurrentPage() {
		$link = "http";
		$link .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
		$link .= "://";
		$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
		(($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
		$link .= $_SERVER["SERVER_NAME"] . $serverPort . $_SERVER["REQUEST_URI"];
		return $link;
	}


}