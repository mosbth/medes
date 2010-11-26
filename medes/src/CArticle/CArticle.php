<?php
class CArticle{
  // file to include...
  protected static $pages = array(
    "view" => array("file"=>"view.php", "title"=>"View Article"),
    "new" => array("file"=>"new.php", "title"=>"New Article"),
    "edit" => array("file"=>"edit.php", "title"=>"Edit Article"),
    "install" => array("file"=>"install.php", "title"=>"Install Articles"),
    "delete" => array("file"=>"delete.php"),
    "debug" => array("file"=>"debug.php", "title"=>"Article: print out debug and config information"),
  );

  // array of the article's attributes
  public static $attribute = array(
    "title"=>array("type"=>"text"),
    "author"=>array("type"=>"text"),
    "copyright"=>array("type"=>"text"),
    "description"=>array("type"=>"text"),
    "keywords"=>array("type"=>"text"),
    "article"=>array("type"=>"text")
  );

  public static $db;

  protected function __construct() {;}
  public function __destruct() {;}

  public static function DoIt(){
    // init database in data folder.
    self::$db = new PDO("sqlite:data/CArticle.db");
    self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pp = CPrinceOfPersia::GetInstance();
    // Check and get the durrent page referer
    $p = isset($_GET['p']) && array_key_exists($_GET['p'], self::$pages) ? $_GET['p'] : 'view'; 		

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

  // create a new article based on the incoming post-variables
  public static function SaveNew(){
    $q = 'insert into article values(';
    foreach(self::$attribute as $key=>$value)
      $q .= ':'.$key.', ';
    $q .= 'datetime("now"), null, null);';
    $stmt = self::$db->prepare($q);
    foreach(self::$attribute as $key=>$value)
      $stmt->bindParam(':'.$key, $_POST[$key]);
    $stmt->execute();
  }

  // update an existing article
  public static function Save(){
    $q = 'update article set ';
    foreach(self::$attribute as $key=>$value)
      $q .= $key . '=:' . $key . ', ';
    $q .= 'modified=datetime("now") where rowid = :id;';
    $stmt = self::$db->prepare($q);
    $stmt->bindParam(':id', $_POST['id']);
  
    foreach(self::$attribute as $key=>$value)
      $stmt->bindParam(':'.$key, $_POST[$key]);
    $stmt->execute();
  }

  // set the deleted datetime
  public static function Delete(){
    $q = 'update article set deleted=datetime("now") where rowid = :id;';
    $stmt = self::$db->prepare($q);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
  }

  // returns html for an edit form based on $attributes
  public static function Edit(){
    $q = 'select ';
    foreach(self::$attribute as $key=>$value)
      $q .= $key.',';
    $q = substr($q, 0, -1).' from article where rowid = :id;';
    $stmt = self::$db->prepare($q);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $row = $stmt->fetch();
    $page = '<form method=post>';
    foreach(self::$attribute as $key=>$value)
      $page .= $key.': <textarea name='.$key.'>'.$row[$key].'</textarea><br>';
    $page .= "<input type=hidden value={$_GET['id']} name=id><input type=submit name=doSaveArticle value=Submit></form>";
    return $page;
  }

  // returns a list of all articles that are not deleted with options to view/edit/delete/create new
  public static function ListAll(){
    $q = 'select rowid, title from article where deleted is null;';
    $stmt = self::$db->query($q);
    $page = '';
    while($res = $stmt->fetch()){
      $page .= $res['rowid'].': '.'<a href="?p=view&id='.$res['rowid'].'">'.$res['title'].'</a> <a href="?p=edit&id='.$res['rowid'].'">e</a> <a href="?p=delete&id='.$res['rowid'].'">x</a><br>';
    }
    $page .= '<a href="?p=new">Create a new article</a>';
    return $page;
  }
}
