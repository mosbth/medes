<?php
// ===========================================================================================
//
// File: CBlog.php
//
// Description: A simple blog that uses CArticle and CArticleEditor. The blog is managed
// using CBlogControlPanel.
//
// Author: Mikael Roos
//
// History:
// 2010-11-27: Created
//

class CBlog {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	public $label;
	
	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($label="") {
		$this->label = $label;
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {;}
	

	// ------------------------------------------------------------------------------------
	//
	// Add blogposts. 
	//
	public function AddPosts($aSet) {
		$a = CArticle::GetInstance();
		foreach($aSet as $val) {
			$val['owner'] = $this->label;
			$a->SaveNew($val);
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Get blogposts. 
	//
	public function GetPosts() {
		$p = CArticle::GetInstance()->GetArticles(array('*'), array(), array(), array(""=>"owner='{$this->label}'", "AND"=>"deleted IS NULL"));
		return $p;
	}


	// ------------------------------------------------------------------------------------
	//
	// First page. 
	//
	public static function GetFrontPage() {;}


	// ------------------------------------------------------------------------------------
	//
	// One blog post. 
	//
	public static function GetBlogPost() {;}

/*	
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
*/

}