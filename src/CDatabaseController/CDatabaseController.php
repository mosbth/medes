<?php
/**
 * Database controller, to manage data in a database.
 * @package MedesCore
 */
class CDatabaseController {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
  //protected static $instance = null;
	protected $db = null;
	public $debug = null;
	protected $stmt = null;
	public $numQueries = 0;
	
	
	/**
	 * Constructor
	 */
	public function __construct($dsn, $username = null, $password = null, $driver_options = null) {
    $this->db = new PDO($dsn, $username, $password, $driver_options);
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	}


	/**
	 * Execute a select-query with arguments and return the resultset.
	 */
  public function ExecuteSelectQueryAndFetchAll($query, $params=array()){
   $this->stmt = $this->db->prepare($query);
    
    if($this->debug) {
    	echo "<p>", $this->stmt->debugDumpParams(), print_r($params, true);
    }
    
    $this->numQueries++;
    $this->stmt->execute($params);
    return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
  }


	/**
	 * Execute a SQL-query and ignore the resultset.
	 */
  public function ExecuteQuery($query, $params = array()) {
    $this->stmt = $this->db->prepare($query);

    if($this->debug) {
    	echo "<p>", $this->stmt->debugDumpParams(), print_r($params, true);
    }
    
    $this->numQueries++;
    $this->stmt->execute($params);
  }


	/**
	 * Return last insert id.
	 */
  public function LastInsertId() {
	   return $this->db->lastInsertid();
  }


	// ------------------------------------------------------------------------------------
	//
  // Return rows affected of last INSERT, UPDATE, DELETE
	//
  public function RowCount() {
	   return is_null(self::$stmt) ? self::$stmt : self::$stmt->rowCount();
  }


}
