<?php
// ===========================================================================================
//
// File: CInterceptionFilter.php
//
// Description: Class CInterceptionFilter
// Used to check access, authority.
//
//
// Author: Mikael Roos, mos@bth.se
//


class CInterceptionFilter implements ISingleton {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected static $instance = null;
	protected $iUc;
//	protected $iPc;

	
	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	protected function __construct() { 
		/* $this->iUc = CUserController::GetInstance();
		$this->iPc = CPageController::GetInstanceAndLoadLanguage(__FILE__); */
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }
	
	
	// ------------------------------------------------------------------------------------
	//
	// Singleton, get the instance or create a new one.
	//
	public static function GetInstance() {
		if(self::$instance == NULL) self::$instance = new CInterceptionFilter(); 
		return self::$instance;
	}
	

/*
	// ------------------------------------------------------------------------------------
	//
	// Check if index.php (frontcontroller) is visited, disallow direct access to 
	// @param boolean $test: Variable to test if empty.
	// @param string $msg: Message to display if variable is empty.
	//
	public function ThrowExceptionIfEmpty($test, $msg) {
		if(empty($test)) throw new Exception($msg);
	}
*/
	
/*	
	// ------------------------------------------------------------------------------------
	//
	// Check if index.php (frontcontroller) is visited, disallow direct access to 
	// pagecontrollers
	//
	public function FrontControllerIsVisitedOrDie() {
			
			global $gPage; // Always defined in frontcontroller
			
			if(!isset($gPage)) {
					die($pc->lang['NO_DIRECT_ACCESS']);
			}
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Check if user has signed in or redirect user to sign in page
	//
	public function UserIsSignedInOrRedirectToSignIn() {
			
			if(!$this->iUc->IsAuthenticated()) { 
					$this->iPc->SetSessionMessage('redirectOnSignin', $this->iPc->CurrentURL());
					$this->iPc->SetSessionMessage('infoMessage', $pc->lang['PAGE_NEEDS_SIGIN']);
					$this->iPc->RedirectToModuleAndPage('', 'login', '');
			}
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Check if user belongs to the admin group or is a specific user.
	//
	public function UserIsCurrentUserOrMemberOfGroupAdminOr403($aUserId) {
			
			$pc = $this->iPc;
			
			$isAdmGroup         = $this->iUc->IsAdministrator() ? true : false;
			$isCurrentUser    = ($this->iUc->GetAccountId() == $aUserId) ? true: false;
	
			if(!($isAdmGroup || $isCurrentUser)) {
					$pc->RedirectToModuleAndPage('', 'p403', '', $pc->lang['CURRENT_USER_OR_ADMIN']);
			}
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Check if user belongs to the admin group, or die.
	// IS THIS USED NOW WHEN CUserController is implemented? 
	// MAY BE OSBSOLETE, should redirect to 403?
	//
	public function UserIsMemberOfGroupAdminOrDie() {
			
			if(!$this->iUc->IsAdministrator()) 
					die($pc->lang['NO_AUTHORITY']);
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Check if user belongs to the admin group or is a specific user.
	// IS THIS USED NOW WHEN CUserController is implemented? 
	// MAY BE OSBSOLETE
	//
	public function IsUserMemberOfGroupAdminOrIsCurrentUser($aUserId) {
			
			$isAdmGroup         = $this->iUc->IsAdministrator() ? true : false;
			$isCurrentUser    = ($this->iUc->GetAccountId() == $aUserId) ? true: false;
	
			return $isAdmGroup || $isCurrentUser;
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Custom defined filter.
	// This method enables a custom filter by setting the $aLabel in the session.
	//
	// $aLabel: The label to set in the SESSION. Identifies the filter.
	// $aAction: check | set | unset
	//
	public function CustomFilterIsSetOrDie($aLabel, $aAction='check') {
	
			switch($aAction) {
	
					case 'set': {
							$_SESSION[$aLabel] = $aLabel;            
					} break;
	
					case 'unset': {
							unset($_SESSION[$aLabel]);
					} break;
			
					case 'check':
					default: {
							isset($_SESSION[$aLabel]) 
									or die($pc->lang['USER_DEFINED_FILTER_NOT_ENABLED']);
					} break;
	
			}
	}
*/	

} // End of Of Class