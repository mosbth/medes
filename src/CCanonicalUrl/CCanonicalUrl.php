<?php
/**
 * manage canonical urls in the database.
 * 
 * @package MedesCore
 */
class CCanonicalUrl implements IUsesSQL, IModule {

	/**#@+
	 * @access private
   */

	/**
	 * Reference to the database.
	 * @var CDatabaseController
   */	
	//private $db;

	/**
	 * A can url object as stored in database.
	 * @var array
   */	
	private $current;
	/**#@-*/
 

	/**
	 * Constructor. 
	 */
	public function __construct() {	
	  $this->current = array();
	}
	
	
	/**
	 * Destructor. 
	 */
	public function __destruct() {;}
	

	/**
	 * Magic method to alarm when setting member that does not exists. 
	 */
	public function __set($name, $value) {
		throw new Exception("Setting undefined member: {$name} => {$value}");
	}

	
	/**
	 * Magic method to alarm when getting member that does not exists.
	 * @return mixed
	 */
	public function __get($name) {
		throw new Exception("Getting undefined member: {$name}");
	}

	
	/**
 	 * Implementing interface IModule. Initiating when module is installed.
 	 */
	public function InstallModule() {
	  global $pp;
  	$pp->db->ExecuteQuery(self::SQL('create table can_url'));
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
  		'create table can_url' => 'create table if not exists can_url(id integer primary key autoincrement, url text unique, real_url text)',
  		'select real url for can url' => 'select real_url from can_url where url=?',
  		'select *' => 'select * from can_url',
  		'select * where id' => 'select * from can_url where id=?',
  		'update where id' => 'update can_url set url=?, real_url=? where id=?',
  		'delete where id' => 'delete from can_url where id=?',
  		'insert with values' => 'insert into can_url (url, real_url) values (?,?)',
  	);
  	if(!isset($query[$id])) {
  		throw new Exception(t('#class error: Out of range. Query = @id', array('#class'=>get_class(), '@id'=>$id)));
		}
		return $query[$id];
	}	


	/**
	 * Load by id. 
	 * @param int $id a integer used as database key to retrieve the canurl object from database.
	 */
	public function LoadById($id) {
	  global $pp;
		$res = $pp->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * where id'), array($id));
		$this->current = isset($res[0]) ? $res[0] : array();
		return !empty($this->current) ? $this->current : false;
	}


	/**
	 * List all. 
	 * @returns array with information on urls.
	 */
	public function ListAll() {
	  global $pp;
		return $pp->db->ExecuteSelectQueryAndFetchAll(self::SQL('select *'));
	}


	/**
	 * Save values from current to database. 
	 */
	public function Save() {
	  global $pp;
	  $pp->db->ExecuteQuery(self::SQL('update where id'), array($this->current['url'], $this->current['real_url'], $this->current['id']));
	}


	/**
	 * Create new in database. 
	 */
	public function Create() {
	  global $pp;
	  $pp->db->ExecuteQuery(self::SQL('insert with values'), array($this->current['url'], $this->current['real_url']));
	  $this->current['id'] = $pp->db->LastInsertId();
	}


	/**
	 * Delete current from database. 
	 */
	public function Delete() {
	  global $pp;
	  $pp->db->ExecuteQuery(self::SQL('delete where id'), array($this->current['id']));
	}


	/**
	 * Check if the url is a canonical url. 
	 * @param string $url The url to check.
	 * @returns $string The real url or false if not found.
	 */
  public function CheckUrl($url) {
    global $pp;
    $res = $pp->db->ExecuteSelectQueryAndFetchAll(self::SQL('select real url for can url'), array($url));
    if(empty($res)) {
      return false;
    } else {
      return $res[0]['real_url'];
    }
	}


	/**#@+
	 * Utilities.
	 */
	public function GetId() { return $this->current['id']; }
	public function GetCanUrl() { return $this->current['url']; }
	public function SetCanUrl($val) { $this->current['url'] = $val; }
	public function GetRealUrl() { return $this->current['real_url']; }
	public function SetRealUrl($val) { $this->current['real_url'] = $val; }
	/**#@-*/


}