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

class CArticle implements IDatabaseObject, IInstallable {

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

		// drafts
		"draftTitle",			// text, a draft title
		"draftContent",		// text, a draft content

		// timestamps
		"owner",					// text, who owns this article, should be int and foreign key to user table later on.

		// timestamps
		"published",			// datetime, timestamp for publishing the article
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
	const CREATE_TABLE_ARTICLE = 1;	
	const INSERT_NEW_ARTICLE = 2;
	const UPDATE_ARTICLE = 3;
	const UPDATE_ARTICLE_AS_PUBLISHED = 4;
	const UPDATE_ARTICLE_AS_UNPUBLISHED = 5;
	const UPDATE_ARTICLE_AS_DELETED = 6;
	const UPDATE_ARTICLE_AS_RESTORED = 7;
	const UPDATE_ARTICLE_UNSET_DRAFT = 8;
	const UPDATE_CHANGE_KEY = 9;
	const SELECT_ARTICLE_BY_ID = 10;
	const SELECT_ARTICLE_BY_KEY = 11;
	const SELECT_ARTICLE_ID_BY_KEY = 12;


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
	// Get SQL that this object support. 
	//
  public static function GetSQL($which) {
  	switch($which) {
  		case self::CREATE_TABLE_ARTICLE:
  			return 'create table if not exists article(id integer primary key autoincrement, key text unique, type text, title text, content text, draftTitle text, draftContent text, owner text, published datetime, created datetime, modified datetime, deleted datetime)';	
  			break;
  		case self::INSERT_NEW_ARTICLE:
  			return 'insert into article(%s,owner,published,created,modified,deleted) values(%s,?,null,datetime("now"),null,null)';
  			break;
  		case self::UPDATE_ARTICLE:
  			return 'update article set modified=datetime("now") %s where id=?';
  			break;
  		case self::UPDATE_ARTICLE_AS_PUBLISHED:
  			return 'update article set published=datetime("now") where id=?';
  			break;
  		case self::UPDATE_ARTICLE_AS_UNPUBLISHED:
  			return 'update article set published=null where id=?';
  			break;
  		case self::UPDATE_ARTICLE_AS_DELETED:
  			return 'update article set deleted=datetime("now") where id=?';
  			break;
  		case self::UPDATE_ARTICLE_AS_RESTORED:
  			return 'update article set deleted=null where id=?';
  			break;
			case self::UPDATE_ARTICLE_UNSET_DRAFT:
  			return 'update article set draftTitle=null, draftContent=null where id=?';
  			break;
  		case self::UPDATE_CHANGE_KEY:
  			return 'update article set key=? where key=?';
  			break;
			case self::SELECT_ARTICLE_BY_ID:
  			return 'select * from article where id=?';
  			break;
  		case self::SELECT_ARTICLE_BY_KEY:
  			return 'select * from article where key=?';
  			break;
  		case self::SELECT_ARTICLE_ID_BY_KEY:
  			return 'select id from article where key=?';
  			break;
  		default:
				throw new Exception(get_class() . " error: GetSQL() out of range.");
				break;
  	}
	}


	// ------------------------------------------------------------------------------------
	//
	// Install. 
	//
  public function Install() {
  	$this->db->ExecuteQuery(self::GetSQL(self::CREATE_TABLE_ARTICLE));
	}


	// ------------------------------------------------------------------------------------
	//
	// Load article from db. 
	// Use $this->current to find out which article to load. 
	//
  public function Load() {

		// Article has id, use it
		if(isset($this->current['id'])) {
			$this->res = $this->db->ExecuteSelectQueryAndFetchAll(self::GetSQL(self::SELECT_ARTICLE_BY_ID), array($this->current['id']));
		}
		
		// Article has key, use it
		else if(isset($this->current['key'])) {
			$this->res = $this->db->ExecuteSelectQueryAndFetchAll(self::GetSQL(self::SELECT_ARTICLE_BY_KEY), array($this->current['key']));
		} 
		
		else {
			throw new Exception(get_class() . " error: Load() article without id or key.");
		}
		
		// Max one item is returned, set this as current
		if(empty($this->res[0]) && isset($this->current['key'])) {
			$key = $this->current['key'];
			$this->ClearCurrent();
			$this->current['key'] = $key;
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
			unset($a['id'], $a['published'], $a['created'], $a['modified'], $a['deleted']);
			foreach($a as $key=>$val) {
				if(!isset($a[$key])) {
					unset($a[$key]);
				}
			}
			
			$uc = CUserController::GetInstance();
			$a['owner'] = $uc->IsAuthenticated() ? $uc->GetAccountName() : "root";

			$q = sprintf(self::GetSQL(self::INSERT_NEW_ARTICLE), implode(",", array_keys($a)), implode(",", array_fill(1,sizeof($a), "?")));
			$this->db->ExecuteQuery($q, array_values($a));
			$this->SetId($this->db->LastInsertId());
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Update existing article in db. 
	//
	public function Update() {
		if(!$this->GetId()) {
			throw new Exception(get_class() . " error: Update() without id set.");
		}
		
		$a = $this->current;
		unset($a['id'], $a['published'], $a['created'], $a['modified'], $a['deleted']);
		$assign = "";
		foreach($a as $key=>$val) {
			if(!isset($a[$key])) {
				unset($a[$key]);
			} else {
				$assign .= ",{$key}=?";
			}
		}
		$a['id'] = $this->GetId();
		$q = sprintf(self::GetSQL(self::UPDATE_ARTICLE), $assign);
		$this->db->ExecuteQuery($q, array_values($a));
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Save article to db. 
	//
	public function Save() {

		// Article has id, do update
		if($this->GetId()) {
			$this->Update();
		} 
		
		// Article has key, do update if exists or insert it
		if($this->GetKey()) {
			$res = $this->db->ExecuteSelectQueryAndFetchAll(self::GetSQL(self::SELECT_ARTICLE_ID_BY_KEY), array($this->GetKey()));
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
	}


	// ------------------------------------------------------------------------------------
	//
	// Delete object from database. 
	// $really: Put object in wastebasket (false) or really delete row from table (true)
	//
	public function Delete($really=false) {
		if(!$this->GetId()) throw new Exception(__METHOD__ . " error: No id set.");				
		$this->db->ExecuteQuery(self::GetSQL(self::UPDATE_ARTICLE_AS_DELETED), array($this->GetId()));
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Restore a deleted object.
	//
	public function Restore() {
		if(!$this->GetId()) throw new Exception(__METHOD__ . " error: No id set.");				
		$this->db->ExecuteQuery(self::GetSQL(self::UPDATE_ARTICLE_AS_RESTORED), array($this->GetId()));
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Rename article key. 
	//
	public function RenameKey($key, $newKey) {
		$this->db->ExecuteQuery(self::GetSQL(self::UPDATE_CHANGE_KEY), array($newKey, $key));
		if($this->db->RowCount() == 1) {
			return true;
		} else {
			return "Failed.";
		}
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Publish article. 
	//
	public function Publish() {
		if(!$this->GetId()) throw new Exception(__METHOD__ . " error: No id set.");				
		$this->db->ExecuteQuery(self::GetSQL(self::UPDATE_ARTICLE_AS_PUBLISHED), array($this->GetId()));
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Unpublish article. 
	//
	public function Unpublish() {
		if(!$this->GetId()) throw new Exception(__METHOD__ . " error: No id set.");				
		$this->db->ExecuteQuery(self::GetSQL(self::UPDATE_ARTICLE_AS_UNPUBLISHED), array($this->GetId()));
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Remove draft article. 
	//
	public function UnsetDraft() {
		if(!$this->GetId())	throw new Exception(get_class() . " error: UnsetDraft() without id set.");
		$this->db->ExecuteQuery(self::GetSQL(self::UPDATE_ARTICLE_UNSET_DRAFT), array($this->GetId()));
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

	public function SetTitle($value) { $this->current['title'] = $value; }
	public function GetTitle() { return $this->current['title']; }

	public function SetContent($value) { $this->current['content'] = $value; }
	public function GetContent() { return $this->current['content']; }

	public function SetDraftTitle($value) { $this->current['draftTitle'] = $value; }
	public function GetDraftTitle() { return $this->current['draftTitle']; }

	public function SetDraftContent($value) { $this->current['draftContent'] = $value; }
	public function GetDraftContent() { return $this->current['draftContent']; }

	public function SetType($value) { $this->current['type'] = $value; }
	public function GetType() { return $this->current['type']; }

	public function GetOwner() { return $this->current['owner']; }
	public function GetPublished() { return $this->current['published']; }
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