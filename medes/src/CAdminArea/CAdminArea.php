<?php
// ===========================================================================================
//
// File: CAdminArea.php
//
// Description: The admin intervafe to CPrinceOfPersia and all modules. It manages settings
// by providing a webinterface where the user can change the settings and configrations
// available in the $pp-object
//
// Author: Mikael Roos
//
// History:
// 2010-10-28: Created
//

class CAdminArea {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
	protected static $menu = array(
		"home" => array("text"=>"/admin area/", "url"=>"adm.php", "title"=>"Administrate and configure the site and its addons"),
		"sep0" => array("text"=>"-", "url"=>"#"),
		"changepwd" => array("text"=>"change password", "url"=>"?p=changepwd", "title"=>"Change the administrator password"),
		"sep1" => array("text"=>"-", "url"=>"#"),
		"sitelink" => array("text"=>"site link", "url"=>"?p=sitelink", "title"=>"Set the main link to the site"),
		"meta" => array("text"=>"meta",  "url"=>"?p=meta", "title"=>"Set default meta tags to enhace search enginge visibility"),
		"tracker" => array("text"=>"tracker",  "url"=>"?p=tracker", "title"=>"Track site using Google Analytics"),
		"sep2" => array("text"=>"-", "url"=>"#"),
//		"item5" => array("text"=>"top-left navigation", "url"=>"#"),
		"header" => array("text"=>"header", "url"=>"?p=header", "title"=>"Define the header and logo of the site"),
		"navbar" => array("text"=>"navigation bar", "url"=>"?p=navbar", "title"=>"Define the navigation bar (main menu) of the site"),
		"footer" => array("text"=>"footer", "url"=>"?p=footer", "title"=>"Define the footer of the site"),
//		"item7" => array("text"=>"footer", "url"=>"#"),
//		"sep3" => array("text"=>"-", "url"=>"#"),
//		"item8" => array("text"=>"addons enable/disable", "url"=>"#"),
		"sep4" => array("text"=>"-", "url"=>"#"),
		"fileupload" => array("text"=>"fileupload", "url"=>"?p=fileupload", "title"=>"Upload files and images"),
		"sep5" => array("text"=>"-", "url"=>"#"),
		"debug" => array("text"=>"debug", "url"=>"?p=debug", "title"=>"Print out debug information and current configuration"),
	);

	protected static $pages = array(
		"home" => array("file"=>"home.php", "title"=>"Home of admin area"),
		"changepwd" => array("file"=>"changepwd.php", "title"=>"Admin area: change password"),
		"sitelink" => array("file"=>"sitelink.php", "title"=>"Admin area: set sitelink"),
		"meta" => array("file"=>"meta.php", "title"=>"Admin area: set meta information"),
		"tracker" => array("file"=>"tracker.php", "title"=>"Admin area: enable tracking using Google Analytics"),
		"header" => array("file"=>"header.php", "title"=>"Admin area: define the header of the site)"),
		"navbar" => array("file"=>"navbar.php", "title"=>"Admin area: set navigation bar, the main menu)"),
		"footer" => array("file"=>"footer.php", "title"=>"Admin area: define the footer of the site)"),
		"fileupload" => array("file"=>"fileupload.php", "title"=>"Admin area: upload files and images)"),
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
		
		// Check and get the durrent page referer
		$p = isset($_GET['p']) && array_key_exists($_GET['p'], self::$pages) ? $_GET['p'] : 'home'; 		
		
		// Set the current menu choice to active
		self::$menu[$p]['active'] = 'active';

		// Prepare the html for the page
		$pp->pageTitle = self::$pages[$p]['title'];
		$access = $pp->GainOrLooseAdminAccess();
		$menu = CNavigation::GenerateMenu(self::$menu, false, 'sidemenu');
		
		// Process the actual page and fill in $page
		require(dirname(__FILE__) . "/" . self::$pages[$p]['file']);

		// Return the resulting page
		$html = <<<EOD
<aside>
	{$access}
	{$menu}
</aside>
<article>
	{$page}
</article>	
EOD;

		return $html;
	}


}