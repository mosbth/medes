<?php
// ===========================================================================================
//
// File: CBlogControlPanel.php
//
// Description: A control panel to manage the CBlog.
//
// Author: Mikael Roos
//
// History:
// 2010-11-27: Created
//

class CBlogControlPanel implements IFrontController {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
	protected static $menu = array(
		"home" => array("text"=>"/blog control panel/", "url"=>"blog.php", "title"=>"Administrate and configure the site and its addons", "class"=>"nav-h1"),

		"site" => array("text"=>"Configure site", "title"=>"Configure and define site related items", "class"=>"nav-h2 nolink"),
		"siteurl" => array("text"=>"site link", "url"=>"?p=siteurl", "title"=>"Set the main link to the site"),
		"meta" => array("text"=>"meta",  "url"=>"?p=meta", "title"=>"Set default meta tags to enhace search enginge visibility"),
		"tracker" => array("text"=>"tracker",  "url"=>"?p=tracker", "title"=>"Track site using Google Analytics"),
		"htmlparts" => array("text"=>"htmlparts", "url"=>"?p=htmlparts", "title"=>"Change htmlparts of site, including header and footer"),
		"navigation" => array("text"=>"navigation", "url"=>"?p=navigation", "title"=>"Define the site navigation menus, including your own navigational menus"),
		"stylesheet" => array("text"=>"stylesheet", "url"=>"?p=stylesheet", "title"=>"Set and edit the stylesheet"),
		"debug" => array("text"=>"debug", "url"=>"?p=debug", "title"=>"Print out debug information and current configuration"),

		"addons" => array("text"=>"Configure addons", "title"=>"Install, update and configure addons", "class"=>"nav-h2 nolink"),
		"fileupload" => array("text"=>"fileupload", "url"=>"?p=fileupload", "title"=>"Upload files and images"),

		"other" => array("text"=>"Other", "title"=>"Other things, to be removed?", "class"=>"nav-h2 nolink"),
		"changepwd" => array("text"=>"change password", "url"=>"?p=changepwd", "title"=>"Change the administrator password"),
	);

	protected static $pages = array(
		"home" => array("file"=>"home.php", "title"=>"Home of blog control panel"),
	);


	// ------------------------------------------------------------------------------------
	//
	// Public internal variables
	//
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	protected function __construct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Frontcontroller. Redirect to choosen page and return the resulting html. 
	//
	public static function DoIt() {
		
		$pp = CPrinceOfPersia::GetInstance();
		
		// Check and get the current page referer
		$p = isset($_GET['p']) && array_key_exists($_GET['p'], self::$pages) ? $_GET['p'] : 'home'; 		
		
		// Set the current menu choice to active
		self::$menu[$p]['active'] = 'active';

		// Prepare the html for the page
		$pp->pageTitle = self::$pages[$p]['title'];
		$sidemenu = CNavigation::GenerateMenu(self::$menu, false, 'sidemenu');
		
		// Process the actual page and fill in $page
		require(dirname(__FILE__) . "/" . self::$pages[$p]['file']);

		// Create the resulting page
		$html = <<<EOD
<article class="span-18">
	{$page}
</article>	
<aside class="span-6 last">
	{$sidemenu}
</aside>
EOD;

		return $html;
	}


}