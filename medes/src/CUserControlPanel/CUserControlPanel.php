<?php
// ===========================================================================================
//
// File: CUserControlPanel.php
//
// Description: The admin interface for the account holders that manages login, logout 
// and account profiles.
//
// Author: Mikael Roos
//
// History:
// 2010-11-25: Created
//

class CUserControlPanel implements iFrontController {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
	protected static $menu = array(
		"home" => array("text"=>"/user control panel/", "url"=>"ucp.php", "title"=>"Manage the settings of the account", "class"=>"nav-h1"),
		"login" => array("text"=>"login", "url"=>"?p=login", "title"=>"Login"),
		"logout" => array("text"=>"logout", "url"=>"?p=logout", "title"=>"Logout"),
		//"changepwd" => array("text"=>"change password", "url"=>"?p=changepwd", "title"=>"Change the administrator password"),
	);

	protected static $pages = array(
		"home" => array("file"=>"home.php", "title"=>"User Control Panel"),
		"login" => array("file"=>"login.php", "title"=>"Login as a registrered user"),
		"logout" => array("file"=>"logout.php", "title"=>"Logout"),
		"changepwd" => array("file"=>"changepwd.php", "title"=>"Change password"),
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
		$menu = CNavigation::GenerateMenu(self::$menu, false, 'sidemenu');
		
		// Process the actual page and fill in $page
		require(dirname(__FILE__) . "/" . self::$pages[$p]['file']);

		// Return the resulting page
		$html = <<<EOD
<article class="span-18">
	{$page}
</article>	
<aside class="span-6 last">
	{$menu}
</aside>
EOD;

		return $html;
	}


}