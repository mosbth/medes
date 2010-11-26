<?php
// ===========================================================================================
//
// File: CUserController.php
//
// Description: Keep values for an authenticated user. This is used to hold information on the
// user. An object is instantiated and populated when the user loggs in. The object is stored
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


class CUserController implements iSingleton {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected static $instance = null;
	protected $settings = array();
	protected $accountId = null;
	protected $accountName = null;


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
			if(isset($_SESSION['uc'])) {
				self::$instance = $_SESSION['uc'];
			} else {
				self::$instance = new CUserController();            
			}
		}
		return self::$instance;
	}


	// ------------------------------------------------------------------------------------
	//
	// Store object in session
	//
	public function StoreInSession() { 
		$_SESSION['uc'] = $this;
	}


	// ------------------------------------------------------------------------------------
	//
	// Populate object when user signs in
	//
	public function Populate($aAccountName, $aAccountId, $aSettings=array()) { 
		$this->accountId		= $aAccountId;
		$this->accountName	= $aAccountName;
		$this->settings			= $aSettings;
	}


	// ------------------------------------------------------------------------------------
	//
	// Is user authenticated?
	//
	public function IsAuthenticated() {
		return empty($this->accountId) ? false : true;
	}


	// ------------------------------------------------------------------------------------
	//
	// Is user a administrator?
	//
	public function IsAdministrator() {
		//return $this->IsMemberOfGroup('admin');
		return $this->accountId == 1;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get account id.
	//
	public function GetAccountId() {
		return $this->accountId;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get account name.
	//
	public function GetAccountName() {
		return $this->accountName;
	}

}