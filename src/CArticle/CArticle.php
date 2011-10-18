<?php
/**
 * Store articles, pieces of information/content in the database.
 * 
 * @package MedesCore
 */
class CArticle implements IUsesSQL, IModule {

	/**#@+
	 * @access private
   */

	/**
	 * Reference to the database.
	 * @var CDatabaseController
   */	
	private $db;
	/**#@-*/
 
 
	/**#@+
	 * @access public
   */
	 
	/**
	 * Remember last resultset.
	 * @var array
   */	
	public $res;

	/**
	 * Holder of current (one) article.
	 * @var array
   */	
	public $current;
	
	/**
	 * Columns of table article.
	 * @var array
   */	
	public $articleCols = array(
		// basics
		"id",		 					// int primary key auto_increment 
		"key", 						// text unique key,
		"type", 					// text, use to identify articles of various content types such as article, blog, news, page, etc.
		"title",					// text, the title of the article
		"url",						// text, the canonical url to this article
		"content",				// text, the actual content of the article
		"filter",				  // text, function to use to filter the content, is it text, html or php or other?

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
	 /**#@-*/


	/**
	 * Constructor. 
	 */
	public function __construct() {
		global $pp;
		$this->db = $pp->db;
		$this->ClearCurrent();
	}
	
	
	/**
	 * Destructor. 
	 */
	public function __destruct() {;}
	

	/**
	 * Magic method to alarm when setting member that does not exists. 
	 */
	public function __set($name, $value) {
		echo get_class() . ": Setting undefined member: {$name} => {$value}";
	}

	
	/**
	 * Magic method to alarm when getting member that does not exists.
	 * @return mixed
	 */
	public function __get($name) {
		throw new Exception(get_class() . ": Getting undefined member: {$name}");
	}

	
	/**
 	 * Implementing interface IModule. Initiating when module is installed.
 	 */
	public function InstallModule() {
  	$this->db->ExecuteQuery(self::SQL('create table article'));
	}
	

	/**
 	 * Implementing interface IModule. Cleaning up when module is deinstalled.
 	 */
	public function DeinstallModule() {
	}
	

	/**
 	 * Implementing interface IModule. Called when updating to newer versions.
 	 */
	public function UpdateModule() {
	}
	
	
	/**
	 * Implementing interface IUsesSQL. Encapsulate all SQL used by this class.
	 *
	 * @param string $id the string that is the key of a SQL-entry in the array
	 */
  public static function SQL($id=null) {
  	$query = array(
  		'create table article' => 'create table if not exists article(id integer primary key autoincrement, key text unique, type text, title text, url text, content text, filter text, draftTitle text, draftContent text, owner text, published datetime, created datetime, modified datetime, deleted datetime)',
  		'insert new article' => 'insert into article(%s,owner,published,created,modified,deleted) values(%s,?,null,datetime("now"),null,null)',
  		'update article' => 'update article set modified=datetime("now") %s where id=?',
  		'update article as published' => 'update article set published=datetime("now") where id=?',
  		'update article as unpublished' => 'update article set published=null where id=?',
  		'update article as deleted' => 'update article set deleted=datetime("now") where id=?',
  		'update article as restored' => 'update article set deleted=null where id=?',
			'update article unset draft' => 'update article set draftTitle=null, draftContent=null where id=?',
  		'update change key' => 'update article set key=? where key=?',
			'select article by id' => 'select * from article where id=?',
  		'select article by key' => 'select * from article where key=?',
  		'select article id by key' => 'select id from article where key=?',
  		'select * by type' => 'select * from article where type=?',
  	);
  	if(!isset($query[$id])) {
  		throw new Exception(t('#class error: Out of range. Query = @id', array('#class'=>get_class(), '@id'=>$id)));
		}
		return $query[$id];
	}	


	/**
	 * Load article from db. Use $this->current to find out which article to load. 
	 * @param int The id to load, overrides $current.
	 * @returns boolean wether succeeded with loading or not.
	 */
  public function Load($id = null) {

		// id provided, use it
		if(!empty($id)) {
			$this->current['id'] = $id;
		}
		
		// Article has id, use it
		if(isset($this->current['id'])) {
			$this->res = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select article by id'), array($this->current['id']));
		}
		
		// Article has key, use it
		else if(isset($this->current['key'])) {
			$this->res = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select article by key'), array($this->current['key']));
		} 
		
		else {
			throw new Exception(t('Load() article without id or key.'));
		}
		
		// Max one item is returned, set this as current
		if(empty($this->res[0]) && isset($this->current['key'])) {
			$key = $this->current['key'];
			$this->ClearCurrent();
			$this->current['key'] = $key;
			return false;
		} else if(empty($this->res[0])) {
			$this->ClearCurrent();
			return false;
		} else {		
			$this->current = $this->res[0];
			return true;
		}
	}


	/**
	 * Load article from db, by using key
	 */
  public function LoadByKey($key=null) {		
		if(!empty($key)) {
			$this->SetKey($key);
		}
		$this->SetId(null);
		return $this->Load();
	}
	

	/**
	 * Insert new article to db. 
	 */
	public function Insert() {
			$a = $this->current;
			unset($a['id'], $a['published'], $a['created'], $a['modified'], $a['deleted']);
			foreach($a as $key=>$val) {
				if(!isset($a[$key])) {
					unset($a[$key]);
				}
			}
			
			$uc = CUserController::GetInstance();
			$a['owner'] = $uc->IsAuthenticated() ? $uc->GetUserAccount() : "root";

			$q = sprintf(self::SQL('insert new article'), implode(",", array_keys($a)), implode(",", array_fill(1,sizeof($a), "?")));
			$this->db->ExecuteQuery($q, array_values($a));
			$this->SetId($this->db->LastInsertId());
	}
	

	/**
	 * Update existing article in db. 
	 */
	public function Update() {
		if(!$this->GetId()) {
			throw new Exception(t('Update() without id set.'));
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
		$q = sprintf(self::SQL('update article'), $assign);
		$this->db->ExecuteQuery($q, array_values($a));
	}
	

	/**
	 * Save article to db. 
	 */
	public function Save() {
		// Article has id, do update
		if($this->GetId()) {
			$this->Update();
		} 
		
		// Article has key, do update if exists or insert it
		if($this->GetKey()) {
			$res = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select article id by key'), array($this->GetKey()));
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


	/**
	 * Delete object from database. 
	 * @param boolean $really  Put object in wastebasket (false) or really delete row from table (true)
	 */
	public function Delete($really=false) {
		if(!$this->GetId()) throw new Exception(t('No id set.'));				
		$this->db->ExecuteQuery(self::SQL('update article as deleted'), array($this->GetId()));

		if($really) die('Delete($really=true) not implemented');
	}
	

	/**
	 * Restore a deleted object.
	 */
	public function Restore() {
		if(!$this->GetId()) throw new Exception(t('No id set.'));				
		$this->db->ExecuteQuery(self::SQL('update article as restored'), array($this->GetId()));
	}
	

	/**
	 * List all articles of specified type. 
	 * @param string $type the type of articles to show.
	 * @returns array with information on articles.
	 */
	public function ListByType($type) {
		return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * by type'), array($type));
	}


	/**
	 * Rename article key. 
	 */
	public function RenameKey($key, $newKey) {
		$this->db->ExecuteQuery(self::SQL('update change key'), array($newKey, $key));
		if($this->db->RowCount() == 1) {
			return true;
		} else {
			return "Failed.";
			//return false;
		}
	}
	

	/**
	 * Publish article. 
	 */
	public function Publish() {
		if(!$this->GetId()) throw new Exception(t('No id set.'));				
		$this->db->ExecuteQuery(self::SQL('update article as published'), array($this->GetId()));
	}
	

	/**
	 * Unpublish article. 
	 */
	public function Unpublish() {
		if(!$this->GetId()) throw new Exception(t('No id set.'));				
		$this->db->ExecuteQuery(self::SQL('update article as unpublished'), array($this->GetId()));
	}
	

	/**
	 * Remove draft article. 
	 */
	public function UnsetDraft() {
		if(!$this->GetId())	throw new Exception(t('No id set.'));
		$this->db->ExecuteQuery(self::SQL('update article unset draft'), array($this->GetId()));
	}
	

	/**
	 * Clear current article. 
	 */
	public function ClearCurrent() {
		$this->current = array_fill_keys($this->articleCols, null);
	}


	/**
	 * Setters and getters
	 */
	public function SetId($value) { $this->current['id'] = $value; }
	public function GetId() {	return $this->current['id']; }

	public function SetKey($value) { $this->current['key'] = $value; }
	public function GetKey() { return $this->current['key']; }

	public function SetTitle($value) { $this->current['title'] = $value; }
	public function GetTitle() { return $this->current['title']; }

	public function SetContent($value) { $this->current['content'] = $value; }
	public function GetContent() { return $this->current['content']; }

	public function SetFilter($value) { $this->current['filter'] = $value; }
	public function GetFilter() { return $this->current['filter']; }

	public function SetCanonicalUrl($value) { $this->current['url'] = $value; }
	public function GetCanonicalUrl() { return $this->current['url']; }

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