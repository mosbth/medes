<?php
// ===========================================================================================
//
// File: CArticle.php
//
// Description: Store articles in the database, makes use of CArticleDB for storing in the 
// database while still preserving a more database neutral interface to its users.
//
// Author: Mikael Roos
//
// History:
// 2010-12-14: Created
//

class CArticle implements IDatabaseObject {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $db;	// CDatabaseController
	public $res;		// remember last resultset
	
	// columns of table article
	public $articleCols = array(
		// basics
		"id",		 					// unique autoincrement id 
		"key", 						// text unique key,
		"type", 					// text, use to identify articles of various content types such as article, blog, news, page, etc.
		"title",					// text, the title of the article
		"content",				// text, the actual content of the article

		// timestamps
		"created",				// datetime, timestamp for creating the article
		"modified",				// datetime, timestamp when article was last modified
		"deleted",				// datetime, timestamp when article was deleted

/* by userid, owner, writer */
/* tags */
/* category */
/* taxeonomy */
/* meta information
		"author"=>array("type"=>"text"),
		"copyright"=>array("type"=>"text"),
		"description"=>array("type"=>"text"),
		"keywords"=>array("type"=>"text"),
*/
	);
	public $article = array();  // holder of one current article
	
	// Predefined SQL statements
	const CREATE_TABLE_ARTICLE = 'create table if not exists article(id int auto_increment, key text unique, type text, title text, content text, created datetime, modified datetime, deleted datetime)';	
	const SELECT_BY_KEY = 'select * from article where key=?';


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {
		$this->db = CDatabaseController::GetInstance();
		$this->article = array_fill_keys($this->articleCols, null);
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {;}
	

	// ------------------------------------------------------------------------------------
	//
	// Install. 
	//
  public function Install() {
  	$this->db->ExecuteQuery(self::CREATE_TABLE_ARTICLE);
	}


	// ------------------------------------------------------------------------------------
	//
	// Save article to db. 
	//
	public function Save() {
		if(isset($this->article['id'])) {
			$q = "update";
		} else {
			$a = $this->article;
			unset($a['id']);
			unset($a['created']);
			unset($a['modified']);
			unset($a['deleted']);
			foreach($a as $key=>$val) {
				if(!isset($a[$key])) {
					unset($a[$key]);
				}
			}
			$q = "insert into article(".implode(",", array_keys($a)).",created,modified,deleted) values(".implode(",", array_fill(1,sizeof($a), "?")).',datetime("now"),null,null)';
			$this->db->ExecuteQuery($q, array_values($a));
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Get article by owner and key. 
	//
	public function GetByKey($aKey) {
		$this->res = $this->db->ExecuteSelectQueryAndFetchAll(self::SELECT_BY_KEY, array($aKey));
		return $this->res;
	}


	// ------------------------------------------------------------------------------------
	//
	// Set the content of an article. 
	//
	public function SetContent($aContent) {
		$this->article['content'] = $aContent;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get the content of an article. 
	//
	public function GetContent() {
		return $this->article['content'];
	}











	// ------------------------------------------------------------------------------------
	//
	// Get all articles. 
	//
  public function GetArticles($attributes=array('*'), $order=array(), $range=array('limit'=>10), $where=array()){
		return $this->adb->GetArticles($attributes, $order, $range, $where);
	}


	// ------------------------------------------------------------------------------------
	//
	// Save a new article. 
	//
  public function SaveNew($attributes){
		return $this->adb->SaveNew($attributes);
	}


	// ------------------------------------------------------------------------------------
	//
	// Delete all articles by owner. 
	//
	public function DeleteAllByOwner($aOwner) {
		$this->adb->DeleteAllByOwner($aOwner);
	}

}