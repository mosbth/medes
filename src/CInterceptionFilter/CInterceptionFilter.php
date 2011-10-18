<?php
/**
 * Check access and authority and take measurements.
 * 
 * @package MedesCore
 */
class CInterceptionFilter {

	/**#@+
	 * @access private
   */

	/**
	 * 
	 * @var string
   */	
	//protected $iUc;
  //protected $iPc;
	/**#@-*/

	
	/**
	 * Constructor
	 */
	public function __construct() { ; }
	
	
	/**
   * Check if user has signed in or redirect user to sign in page
	 */
	static public function UserIsSignedInOrRedirectToSignIn() {
	  global $pp;
    if(!$pp->uc->IsAuthenticated()) {
      echo "need to login";      
    }
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