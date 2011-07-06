<?php
// ===========================================================================================
//
// File: CUserController.php
//
// Description: Keep values for an authenticated user. This is used to hold information on the
// user. An object is instantiated and populated when the user login. The object is stored
// in the session and used together with CIntercetptionFilter, to verify 
// authority.
// Class is implemented as a Singelton-like where the getInstance gets the current instance from 
// $_SESSION. 
//
// Author: Mikael Roos
//
// History:
// 2010-11-26: Created
//


class CUserController implements iSingleton, IUsesSQL, IModule {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	const sessionName = 'mds-uc';
	private static $instance = null;
	//private $settings = array();
	private $userId = null;
	private $userAccount = null;
	private $userEmail = null;


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
	// Singleton pattern.
	// Get the instance of the latest created object or create a new one. 
	//
	public static function GetInstance() {
		if(self::$instance == null) {
			if(isset($_SESSION[self::sessionName])) {
				self::$instance = $_SESSION[self::sessionName];
			} else {
				self::$instance = new CUserController();            
			}
		}
		return self::$instance;
	}


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
	 * Store object in session
	 */
	public function StoreInSession() { 
		$_SESSION[self::sessionName] = $this;
	}


	/**
 	 * Implementing interface IModule. Initiating when module is installed.
 	 */
	public function InstallModule() {
		global $pp;
  	$pp->db->ExecuteQuery(self::SQL('create table user'));		
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
  		'create table user' => 'create table if not exists user(id integer, account text, email text, password text, salt text, algorithm text, primary key(id))',
  		'get user' => 'select id,account,email,password,salt,algorithm from user where account=?',
  	);
  
  	if(!isset($query[$id])) {
  		throw new Exception(t('#class error: Out of range. Query = @id', array('#class'=>get_class(), '@id'=>$id)));
		}
		
		return $query[$id];
	}	


	/**
	 * Login, try to authenticate user and store in session if successful
	 *
	 * @param string $aUser the username/account to login with
	 * @param string $aPassword the password
	 */
	public function Login($aUser, $aPassword) {
		global $pp;
		$q = $this->SQL('get user');
		$user = $pp->db->ExecuteSelectQueryAndFetchAll($q, array($aUser));
		$user = $user[0];
		if($this->CheckPassword($aPassword, $user['algorithm'], $user['password'], $user['salt'])) {
			$this->userId				= $user['id'];
			$this->userAccount	= $user['account'];
			$this->userEmail		= $user['email']; 
			// Get additional settings
			//$this->settings			= $aSettings;
			$this->StoreInSession();
			return true;
		} 
		return false;		
	}
	
	
	/**
	 * Check a password using the specified algorithm
	 *
	 * @param string $aPwd the password to check
	 * @param string $aAlgorithm what algorithm to use when checking
	 * @param string $aPassword the users current password
	 * @param string $aSalt the salt, if any
	 */
	public function CheckPassword($aPwd, $aAlgorithm, $aPassword, $aSalt) {
		switch($aAlgorithm) {
			case 'plain':
				return $aPwd == $aPassword;
				break;
			case 'md5':
				return md5($aSalt . $aPwd) == $aPassword;
				break;
			case 'md5-nosalt':
				return md5($aPwd) == $aPassword;
				break;
			case 'sha1':
				return sha1($aSalt . $aPwd) == $aPassword;
				break;
			default:
				return false;
		}
	}
	
	
	/**
	 * Logout, clear the session
	 *
	 * @param string $aUser the username/account to login with
	 * @param string $aPassword the password
	 */
	public function Logout() {
		global $pp;
		$pp->DestroyAndRestartSession();
	}
	
	
/*
	// ------------------------------------------------------------------------------------
	//
	// Set the administrator password
	//  $aPwd: the password in plain text
	//  $aEncryptionFunction: a function that encrypts the password
	//
	public function SetAdminPassword($aPwd, $aEncryptionFunction='sha1') {
		
		$timestamp = md5(microtime());
		$this->config['password'] = array(
			'function'=>$aEncryptionFunction,
			'timestamp'=>$timestamp,
			'password'=>call_user_func($aEncryptionFunction, $timestamp.$aPwd.$timestamp),
		);
		$this->StoreConfigToFile();
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if password matches the administrator password
	//  $aPwd: the password in plain text
	//
	public function CheckAdminPassword($aPwd) {
		
		$password 	= $this->config['password']['password'];
		$function 	= $this->config['password']['function'];
		$timestamp 	= $this->config['password']['timestamp'];
		return $password == call_user_func($function, $timestamp.$aPwd.$timestamp);
	}
*/


/*
	// ------------------------------------------------------------------------------------
	//
	// Populate object when user signs in
	//
	public function Populate($aAccountName, $aAccountId, $aSettings=array()) { 
		$this->accountId		= $aAccountId;
		$this->accountName	= $aAccountName;
		$this->settings			= $aSettings;
	}
*/

	// ------------------------------------------------------------------------------------
	//
	// Is user authenticated?
	//
	public function IsAuthenticated() {
		return empty($this->userAccount) ? false : true;
	}


	// ------------------------------------------------------------------------------------
	//
	// Is user a administrator?
	//
	public function IsAdministrator() {
		//return $this->IsMemberOfGroup('admin');
		return $this->userId == 1;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get account id.
	//
	public function GetUserId() {
		return $this->userId;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get account name.
	//
	public function GetUserAccount() {
		return $this->userAccount;
	}

}