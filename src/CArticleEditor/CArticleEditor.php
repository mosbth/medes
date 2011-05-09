<?php
class CArticleEditor {
  // ------------------------------------------------------------------------------------
  // 
  // Protected internal variables
  //

  protected static $pages = array(
    "home" => array("file"=>"home.php", "title"=>"Home of ArticleEditor area"),
    "install" => array("file"=>"install.php", "title"=>"Install the article database"),
    "edit" => array("file"=>"edit.php", "title"=>"Edit article"),
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
	
    $CArticle = new CArticle();
    $pp = CPrinceOfPersia::GetInstance();

    // Check and get the durrent page referer
    $p = isset($_GET['p']) && array_key_exists($_GET['p'], self::$pages) ? $_GET['p'] : 'home'; 	

    $menu = array();
    
   if($p != 'install'){ 
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    $articles = $CArticle->GetArticles(array('rowid', 'title'), array(), array('limit'=>'25', 'offset'=>$offset), array('deleted'=>'isnull'));
    $menu["obscure123321"] = array("text"=>"/article editor/", "url"=>"?p=home", "title"=>"Edit articles", "class"=>"nav-h1");
    $menu['obscure123322'] = array("text"=>"New Article", "url"=>"?p=edit", "title"=>"Create a new article", "class"=>"nav-h1");
    foreach ($articles as $row)
    {
    	$menu[$row['title']] = array("text"=>$row['title'], "url"=>"?p=edit&id=".$row['rowid'], "title"=>$row['title']);
    }
    $menu = CNavigation::GenerateMenu($menu, false, 'sidemenu');
   }
    
//    $menu .= '';
//    foreach($articles as $row){
//    	$menu2 .= "<option value={$row['rowid']}>{$row['title']}</option>";
//    }
//    $menu2 = <<<EOD
//    <form method=post action=?p=edit>
//    	<select size=12 class=span-5 name=id>
//    	{$menu2}
//    	</select>
//    	<input type=submit name=loadArticle>
//    </form>
//EOD;

    // Set the current menu choice to active
    //self::$menu[$p]['active'] = 'active';

    // Prepare the html for the page
    $pp->pageTitle = self::$pages[$p]['title'];

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
