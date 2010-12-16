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
	protected $db;		// CDatabaseController
	public $res;			// remember last resultset
	public $current; 	// holder of current (one) article
	
	// columns of table article
	public $articleCols = array(
		// basics
		"id",		 					// int primary key auto_increment 
		"key", 						// text unique key,
		"type", 					// text, use to identify articles of various content types such as article, blog, news, page, etc.
		"title",					// text, the title of the article
		"content",				// text, the actual content of the article

		// timestamps
		"owner",					// text, who owns this article, should be int and foreign key to user table later on.

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
	
	// Predefined SQL statements
	const CREATE_TABLE_ARTICLE = 'create table if not exists article(id integer primary key autoincrement, key text unique, type text, title text, content text, owner text, created datetime, modified datetime, deleted datetime)';	
	const INSERT_NEW_ARTICLE = 'insert into article(%s,owner,created,modified,deleted) values(%s,?,datetime("now"),null,null)';
	const UPDATE_ARTICLE = 'update article set modified=datetime("now") %s where id=?';
	const SELECT_ARTICLE_BY_ID = 'select * from article where id=?';
	const SELECT_ARTICLE_BY_KEY = 'select * from article where key=?';
	const SELECT_ARTICLE_ID_BY_KEY = 'select id from article where key=?';


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {
		$this->db = CDatabaseController::GetInstance();
		$this->ClearCurrent();
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
	// Load article from db. 
	// Use $this->current to find out which article to load. 
	//
  public function Load() {

		// Article has id, use it
		if(isset($this->current['id'])) {
			$this->res = $this->db->ExecuteSelectQueryAndFetchAll(self::SELECT_ARTICLE_BY_ID, array($this->current['id']));
		}
		
		// Article has key, use it
		else if(isset($this->current['key'])) {
			$this->res = $this->db->ExecuteSelectQueryAndFetchAll(self::SELECT_ARTICLE_BY_KEY, array($this->current['key']));		
		} 
		
		else {
			Throw new Exception(get_class() . " error: Load() article without id or key.");
		}
		
		// Max one item is returned, set this as current
		if(empty($this->res[0]) && isset($this->current['key'])) {
			$key = $this->current['key'];
			$this->ClearCurrent();
			$this->current = $key;
		} else if(empty($this->res[0])) {
			$this->ClearCurrent();
		} else {		
			$this->current = $this->res[0];
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Load article from db, by using key
	//
  public function LoadByKey($key=null) {		
		if(!empty($key)) {
			$this->SetKey($key);
		}
		$this->SetId(null);
		$this->Load();
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Insert new article to db. 
	//
	public function Insert() {
			$a = $this->current;
			unset($a['id'], $a['created'], $a['modified'], $a['deleted']);
			foreach($a as $key=>$val) {
				if(!isset($a[$key])) {
					unset($a[$key]);
				}
			}
			$a['owner'] = CUserController::GetInstance()->GetAccountName();
			$q = sprintf(self::INSERT_NEW_ARTICLE, implode(",", array_keys($a)), implode(",", array_fill(1,sizeof($a), "?")));
			$this->db->ExecuteQuery($q, array_values($a));
			$this->SetId($this->db->LastInsertId());
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Update existing article in db. 
	//
	public function Update() {
		if(!$this->GetId()) {
			Throw new Exception(get_class() . " error: Update() without id set.");
		}
		
		$a = $this->current;
		unset($a['id'], $a['created'], $a['modified'], $a['deleted']);
		$assign = "";
		foreach($a as $key=>$val) {
			if(!isset($a[$key])) {
				unset($a[$key]);
			} else {
				$assign .= ",{$key}=?";
			}
		}
		$a['id'] = $this->GetId();
		$q = sprintf(self::UPDATE_ARTICLE, $assign);
		$this->db->ExecuteQuery($q, array_values($a));
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Save article to db. 
	// $loadOnSave: Default behaviour is to reload the inserted/updated article.
	//
	public function Save($loadOnSave=true) {

		// Article has id, do update
		if($this->GetId()) {
			$this->Update();
		} 
		
		// Article has key, do update if exists or insert it
		if($this->GetKey()) {
			$res = $this->db->ExecuteSelectQueryAndFetchAll(self::SELECT_ARTICLE_ID_BY_KEY, array($this->GetKey()));
			if(empty($res)) {
				$this->Insert();
			} else {
				$this->SetId($res[0]['id']);
				$this->Update();
			}
		} 
		
		// Insert new article
		else {
			$this->Insert();
		}
		
		// Load the article that was inserted/updated
		if($loadOnSave) {
			$this->Load();
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Delete object from database. 
	// $really: Put object in wastebasket (false) or really delete row from table (true)
	//
	public function Delete($really=false) {
		;
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Clear current article. 
	//
	public function ClearCurrent() {
		$this->current = array_fill_keys($this->articleCols, null);
	}


	// ------------------------------------------------------------------------------------
	//
	// Setters and getters
	//
	public function SetId($value) { $this->current['id'] = $value; }
	public function GetId() {	return $this->current['id']; }

	public function SetKey($value) { $this->current['key'] = $value; }
	public function GetKey() { return $this->current['key']; }

	public function SetContent($value) { $this->current['content'] = $value; }
	public function GetContent() { return $this->current['content']; }

	public function SetType($value) { $this->current['type'] = $value; }
	public function GetType() { return $this->current['type']; }

	public function GetOwner() { return $this->current['owner']; }
	public function GetCreated() { return $this->current['created']; }
	public function GetModified() { return $this->current['modified']; }
	public function GetDeleted() { return $this->current['deleted']; }





/*

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
*/

}