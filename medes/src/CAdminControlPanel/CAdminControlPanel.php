<?php
// ===========================================================================================
//
// File: CAdminControlPanel.php
//
// Description: The admin interface to CPrinceOfPersia and all modules. It manages settings
// by providing a webinterface where the user can change the settings and configrations
// available in the $pp-object
//
// Author: Mikael Roos
//
// History:
// 2010-10-28: Created
//

class CAdminControlPanel implements IFrontController {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
	protected static $menu = array(
		"home" => array("text"=>"/admin control panel/", "url"=>"acp.php", "title"=>"Administrate and configure the site and its addons", "class"=>"nav-h1"),

		"site" => array("text"=>"Site", "title"=>"Configure and define site related items", "class"=>"nav-h2 nolink"),
		"changepwd" => array("text"=>"change password", "url"=>"?p=changepwd", "title"=>"Change the administrator password"),
		"siteurl" => array("text"=>"site link", "url"=>"?p=siteurl", "title"=>"Set the main link to the site"),
		"meta" => array("text"=>"meta",  "url"=>"?p=meta", "title"=>"Set default meta tags to enhace search enginge visibility"),
		"tracker" => array("text"=>"tracker",  "url"=>"?p=tracker", "title"=>"Track site using Google Analytics"),
		"htmlparts" => array("text"=>"htmlparts", "url"=>"?p=htmlparts", "title"=>"Change htmlparts of site, including header and footer"),
		"navigation" => array("text"=>"navigation", "url"=>"?p=navigation", "title"=>"Define the site navigation menus, including your own navigational menus"),
		"stylesheet" => array("text"=>"stylesheet", "url"=>"?p=stylesheet", "title"=>"Set and edit the stylesheet"),
		"debug" => array("text"=>"debug", "url"=>"?p=debug", "title"=>"Print out debug information and current configuration"),

		"addons" => array("text"=>"Addons", "title"=>"Install, update and configure addons", "class"=>"nav-h2 nolink"),
		"fileupload" => array("text"=>"fileupload", "url"=>"?p=fileupload", "title"=>"Upload files and images"),

//		"other" => array("text"=>"Other", "title"=>"Other things, to be removed?", "class"=>"nav-h2 nolink"),
//		"header" => array("text"=>"header", "url"=>"?p=header", "title"=>"Define the header and logo of the site"),
//		"footer" => array("text"=>"footer", "url"=>"?p=footer", "title"=>"Define the footer of the site"),
//		"relatedsites" => array("text"=>"related sites", "url"=>"?p=relatedsites", "title"=>"Use and define related sites"),
//		"navbar" => array("text"=>"navigation bar", "url"=>"?p=navbar", "title"=>"Define the navigation bar (main menu) of the site"),
	);

	protected static $pages = array(
		"home" => array("file"=>"home.php", "title"=>"Home of admin area"),
		"changepwd" => array("file"=>"changepwd.php", "title"=>"Admin area: change password"),
		"siteurl" => array("file"=>"siteurl.php", "title"=>"Admin area: set sitelink"),
		"meta" => array("file"=>"meta.php", "title"=>"Admin area: set meta information"),
		"tracker" => array("file"=>"tracker.php", "title"=>"Admin area: enable tracking using Google Analytics"),
		"htmlparts" => array("file"=>"htmlparts.php", "title"=>"Admin area: edit htmlparts of the site"),
//		"header" => array("file"=>"header.php", "title"=>"Admin area: define the header of the site"),
//		"footer" => array("file"=>"footer.php", "title"=>"Admin area: define the footer of the site"),
		"navigation" => array("file"=>"navigation.php", "title"=>"Admin area: define and set navigation menus"),
//		"relatedsites" => array("file"=>"relatedsites.php", "title"=>"Admin area: use and define related sites"),
//		"navbar" => array("file"=>"navbar.php", "title"=>"Admin area: set navigation bar, the main menu"),
		"stylesheet" => array("file"=>"stylesheet.php", "title"=>"Admin area: set and edit the stylesheet"),
		"fileupload" => array("file"=>"fileupload.php", "title"=>"Admin area: upload files and images"),
		"debug" => array("file"=>"debug.php", "title"=>"Admin area: print out debug and config information"),
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
<article class="span-18 colborder">
	{$page}
</article>	
<aside class="span-5 last">
	{$sidemenu}
</aside>
EOD;

		return $html;
	}


}