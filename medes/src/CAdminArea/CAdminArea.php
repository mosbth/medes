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
		"item2" => array("text"=>"site link", "url"=>"#"),
		"item3" => array("text"=>"meta", "url"=>"#"),
		"item4" => array("text"=>"tracker", "url"=>"#"),
		"sep2" => array("text"=>"-", "url"=>"#"),
		"item5" => array("text"=>"top-left navigation", "url"=>"#"),
		"item6" => array("text"=>"navigation bar", "url"=>"#"),
		"item7" => array("text"=>"footer", "url"=>"#"),
		"sep3" => array("text"=>"-", "url"=>"#"),
		"item8" => array("text"=>"addons enable/disable", "url"=>"#"),
	);

	protected static $pages = array(
		"home" => array("file"=>"home.php", "title"=>"Home of admin area"),
		"changepwd" => array("file"=>"changepwd.php", "title"=>"Admin area: change password"),
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
		
		// Check and get the durrent page referer
		$p = isset($_GET['p']) && array_key_exists($_GET['p'], self::$pages) ? $_GET['p'] : 'home'; 		
		
		// Set the current menu choice to active
		self::$menu[$p]['active'] = 'active';

		// Prepare the html fot the page
		$access = self::GainOrLooseAdminAccess();
		$menu = CNavigation::GenerateMenu(self::$menu, false, 'sidemenu');
		
		// Create a template page using nowdoc, must end with empty row
		$template = <<<'EOT'
echo <<<EOD
<aside>
	{$access}
	{$menu}
</aside>
<article>
	{$page}
</article>	
EOD;

EOT;

		// Process the actutal page
		require(dirname(__FILE__) . "/" . self::$pages[$p]['file']);
	}


	// ------------------------------------------------------------------------------------
	//
	// Manages to gain or loose the admin access. 
	//
	public static function GainOrLooseAdminAccess() {

		// Try to gain admin access
		if(isset($_GET['doGainAdminAccess'])) {
			$html = <<<EOD
<form action=? method=post>
	<fieldset class=standard>
		<legend>gain admin access</legend>
		<input type=password name=password>
		<input type=submit name=doCheckAdminPassword value=Login>
		<input type=submit name=noAction value=Cancel>
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
	// Frontcontroller to this object, manages what happens. 
	//
	public static function Callback_changepwd() {

		$html = <<<EOD
moped
EOD;
		return $html;
	}


}