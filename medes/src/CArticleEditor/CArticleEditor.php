<?php
class CArticleEditor {
	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//

	protected static $pages = array(
		"home" => array("file"=>"home.php", "title"=>"Home of ArticleEditor area"),
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
		
      $CArticle = CArticle::GetInstance();
		$pp = CPrinceOfPersia::GetInstance();
		
		// Check and get the durrent page referer
		$p = isset($_GET['p']) && array_key_exists($_GET['p'], self::$pages) ? $_GET['p'] : 'home'; 		
		
		// Set the current menu choice to active
		//self::$menu[$p]['active'] = 'active';

		// Prepare the html for the page
		$pp->pageTitle = self::$pages[$p]['title'];
		
		// Process the actual page and fill in $page
		require(dirname(__FILE__) . "/" . self::$pages[$p]['file']);

		// Return the resulting page
		$html = <<<EOD
<article>
	{$page}
</article>	
EOD;

		return $html;
	}
}
