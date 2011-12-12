<?php
/**
 * Setting up definitions for Medes.
 *
 * @package MedesCore
 */

/**
 * Enable auto-load of class declarations.
 */
function autoload($aClassName) {
	$file1 = MEDES_INSTALL_PATH . "/src/{$aClassName}/{$aClassName}.php";
	$file2 = MEDES_INSTALL_PATH . "/site/src/{$aClassName}/{$aClassName}.php";
	if(is_file($file1)) {
		require_once($file1);
	} elseif(is_file($file2)) {
		require_once($file2);
	}
}
spl_autoload_register('autoload');

/**
 * Translation.
 */
function t($key, $arg=null) {
	$t = $key;
	if(isset($arg)) {
		foreach($arg as $key => $val) {
			$t = preg_replace('/' . $key . '/', $val, $t);		
		}
	}
	return $t;
}

/**
 * Sanitizing text to be able to display it in a html-page.
 * @param string text The text to be sanitized.
 * @returns string The sanitized html.
 */
function sanitizeHTML($text) {	
	return htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8', false);
}

/**
 * BBCode formatting converting to HTML.
 * @param string text The text to be converted.
 */
function bbcode2html($text) {
	function phpsyntax($text) {
		return "<blockquote class='code'>".highlight_string(str_replace('#DOLLAR#', '$', trim($text)), true).'</blockquote>';
	};
	function code($text) {
		return "<blockquote class='code'>".nl2br(sanitizeHTML(trim($text)), true).'</blockquote>';
	};
	return preg_replace(
		array(
			'/\\[url[\\:\\=]((\\"([\\W]*javascript\:[^\\"]*)?([^\\"]*)\\")|'.
					'(([\\W]*javascript\:[^\\]]*)?([^\\]]*)))\\]/ie', '/\\[\\/url\\]/i',
			'/\\[b\\]/i', '/\\[\/b\\]/i',
			'/\\[i\\]/i', '/\\[\/i\\]/i',
			'/\\[quote\\]/i', '/\\[\/quote\\]/i',
			'/\[code\](.*?)\[\/code\]/ies',
			'/\[php\](.*?)\[\/php\]/ies',
			'/\[youtube\](.*?)\[\/youtube\]/is',
		),
		array(
			'\'<a href="\'.(\'$4\'?\'$4\':\'$7\').\'">\'', '</a>',
			'<b>', '</b>',
			'<i>', '</i>',
			"<blockquote class='quote'>", '</blockquote>',
			'code("\\1")',
			'phpsyntax("\\1")',
			'<object width="425" height="350">
			 <param name="movie" value="http://www.youtube.com/v/$1"></param>
			 <param name="wmode" value="transparent"></param>
			 <embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed>
			 </object>
			',
		),
		str_replace('$', '#DOLLAR#', $text)
	);
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


// ========= BELOW IS OBSOLETE AND SHOULD BE REMOVED WHEN VERIFIED

/**
 * Interface for class that interacts with the database. 
 * @depreciated (?)
 */
//interface IDatabaseObject {

	/**
	 * Get SQL that this object support. 
	 * @return string containing the SQL-code.
	 */
  //public static function GetSQL($which=null);

	/**
	 * Insert new object to database. 
	 * @return boolean true on success, else false.
	 */
	//public function Insert();
	
	/**
	 * Update existing object in database. 
	 * @return boolean true on success, else false.
	 */
	//public function Update();
	
	/**
	 * Save object to database. Manage if insert or update.
	 * @return boolean true on success, else false.
	 */
	//public function Save();
	
	/**
	 * Load object from database.
	 * @return boolean true on success, else false.
	 */
	//public function Load();
	
	/**
	 * Delete object from database.
	 * @param boolean $really Put object in wastebasket (false) or really delete row from table (true).
	 * @return boolean true on success, else false.
	 */
	//public function Delete($really=false);
//}

/**
 * Interface for classes implementing a frontcontroller (?) pattern.
 * @depreciated
 */
/*interface IFrontController {
	public static function DoIt();
}*/

/**
 * Manage _GET and _POST requests and redirect or return the resulting html. 
 * @depreciated
 */
/*interface IActionHandler {
	public function ActionHandler();
}
*/

/**
 * Gather all language-strings behind one method.
 * @depreciated
 */
/*interface ILanguage {
	public static function InitLanguage($language=null);
}
*/

/**
 * Interface for addons.
 * @depreciated
 */
//interface IAddOn {}
