<?php
/**
 * Setting up definitions for Medes.
 *
 * @package MedesCore
 */

/**
 * Enable auto-load of class declarations.
 */
function __autoload($aClassName) {
	$file = MEDES_INSTALL_PATH . "/src/{$aClassName}/{$aClassName}.php";
	if(is_file($file)) {
		require_once($file);
	}

	// TODO: Also look for files in MEDES_SITE_PATH/src to enable for each site to add autoload classes
}

/**
 * Translation.
 */
function t($key, $arg=null) {
	$t = $key;
	foreach($arg as $key => $val) {
		$t = preg_replace('/' . $key . '/', $val, $t);		
	}
	return $t;
}

/**
 * Interface for classes implementing the singleton pattern.
 */
interface ISingleton {
	public static function GetInstance();
}

/**
 * Interface for modules/addons to the MedesCore. Even parts of MedesCore is modules. 
 */
interface IModule {
	/**
 	 * Implementing interface IModule. Initiating when module is installed.
 	 */
	public function InstallModule();

	/**
 	 * Implementing interface IModule. Cleaning up when module is deinstalled.
 	 */
	public function DeinstallModule();

	/**
 	 * Implementing interface IModule. Called when updating to newer versions.
 	 */
	public function UpdateModule();
}


/**
 * Interface for modules/addons to the MedesCore. Even parts of MedesCore is modules. 
 */
interface IController {
	/**
 	 * Implementing interface IController. All controllers must have an index action.
 	 */
	public function Index();
}


/**
 * Interface for class that interacts with the database. 
 *
 * Contains one method which encapsulates all SQL requests.
 */
interface IUsesSQL {
  public static function SQL($id=null);
}

/**
 * Interface for class that interacts with the database. 
 * @depreciated (?)
 */
interface IDatabaseObject {

	/**
	 * Get SQL that this object support. 
	 * @return string containing the SQL-code.
	 */
  public static function GetSQL($which=null);

	/**
	 * Insert new object to database. 
	 * @return boolean true on success, else false.
	 */
	public function Insert();
	
	/**
	 * Update existing object in database. 
	 * @return boolean true on success, else false.
	 */
	public function Update();
	
	/**
	 * Save object to database. Manage if insert or update.
	 * @return boolean true on success, else false.
	 */
	public function Save();
	
	/**
	 * Load object from database.
	 * @return boolean true on success, else false.
	 */
	public function Load();
	
	/**
	 * Delete object from database.
	 * @param boolean $really Put object in wastebasket (false) or really delete row from table (true).
	 * @return boolean true on success, else false.
	 */
	public function Delete($really=false);
}

/**
 * Interface for classes implementing a frontcontroller (?) pattern.
 * @depreciated
 */
interface IFrontController {
	public static function DoIt();
}

/**
 * Manage _GET and _POST requests and redirect or return the resulting html. 
 * @depreciated
 */
interface IActionHandler {
	public function ActionHandler();
}

/**
 * Gather all language-strings behind one method.
 * @depreciated
 */
interface ILanguage {
	public static function InitLanguage($language=null);
}

/**
 * Interface for addons.
 * @depreciated
 */
interface IAddOn {}
