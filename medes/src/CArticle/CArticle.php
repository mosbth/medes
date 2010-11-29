<?php
class CArticle implements ISingleton{
  public $attribute;
  public $db;

  protected static $instance = null;

  protected function __construct() {
    $pp = CPrinceOfPersia::GetInstance();
    $this->attribute = array(
      "title"=>array("type"=>"text"),
      "author"=>array("type"=>"text"),
      "copyright"=>array("type"=>"text"),
      "description"=>array("type"=>"text"),
      "keywords"=>array("type"=>"text"),
      "article"=>array("type"=>"text"),
      "owner"=>array("type"=>"text")
    );
    $this->db = new PDO("sqlite:{$pp->installPath}/medes/data/CArticle.db");
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  public function __destruct() {unset($this->db);}

  public static function GetInstance(){
    if(self::$instance == NULL)
      self::$instance = new CArticle();
    return self::$instance;
  }

  // create a new article based on the incoming post-variables
  public function SaveNew($attributes){
    $q = 'insert into article values(';
    foreach($this->attribute as $key=>$value)
      $q .= ':'.$key.', ';
    $q .= 'datetime("now"), null, null);';
    $stmt = $this->db->prepare($q);
    foreach($this->attribute as $key=>$value)
      $stmt->bindParam(':'.$key, $attributes[$key]);
    $stmt->execute();
  }

  // update an existing article
  public function Save($attributes){
    $q = 'update article set ';
    foreach($this->attribute as $key=>$value)
      $q .= $key . '=:' . $key . ', ';
    $q .= 'modified=datetime("now") where rowid = :id;';
    $stmt = $this->db->prepare($q);
    $stmt->bindParam(':id', $attributes['id']);
  
    foreach($this->attribute as $key=>$value)
      $stmt->bindParam(':'.$key, $attributes[$key]);
    $stmt->execute();
  }

  // set the deleted datetime
  public function Delete($id){
    $q = 'update article set deleted=datetime("now") where rowid = :id;';
    $stmt = $this->db->prepare($q);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
  }

  public function GetArticles($attributes=array('*'), $order=array(), $range=array('range'=>10), $where=array()){
    $q = 'select ';
    foreach($attributes as $value)
      $q .= $value.', ';
    $q = substr($q, 0, -2).' from article';
    if(sizeof($where) > 0){
      $q .= ' where ';
      foreach($where as $key=>$value)
        $q .= $key . ' ' . $value;
    }
    if(sizeof($order) > 0)
      $q .= ' order by '.$order['by'].' '.(isset($order['sort']) ? $order['sort'] : '');
    if(sizeof($range) > 0)
      $q .= ' limit '.(isset($range['offset']) ? $range['offset'].', ' : '').(isset($range['limit']) ? $range['limit'] : '').';';

    $stmt = $this->db->prepare($q);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $row;
  }

  // create table and insert a sample home-article
  public function Install(){
    $q = 'create table if not exists article(';
    foreach($this->attribute as $key=>$value)
      $q .= $key.' '.$value['type'].',';
    $q .= 'created datetime, modified datetime, deleted datetime);';
    $stmt = $this->db->prepare($q);
    $page = "<p>Executing install of articles db: <br><pre>".$stmt->queryString."</pre>";
    $stmt->execute();

    $q = 'insert into article values(';
    $q .= '"home", "admin", "&copy;2k10, all rights reserved", "description", "home, phpmedes", "<p>Welcome to phpmedes!</p>", "article", datetime("now"), null, null);';
    $stmt = $this->db->prepare($q);
    $page .= "<p>Executing install of articles db: <br><pre>".$stmt->queryString."</pre>";
    $stmt->execute();
    return $page;
  }
}
