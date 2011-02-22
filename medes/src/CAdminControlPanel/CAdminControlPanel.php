<?php
// ===========================================================================================
//
// File: CAdminControlPanel.php
//
// Description: The admin interface to CPrinceOfPersia and all modules. It manages settings
// by providing a webinterface where the user can change the settings and configrations
// available in the $pp-object
//
// Author: Mikael Roos
//
// History:
// 2010-10-28: Created
//

class CAdminControlPanel implements IFrontController {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
	protected static $menu = array(
		"home" => array("text"=>"/admin control panel/", "url"=>"acp.php", "title"=>"Administrate and configure the site and its addons", "class"=>"nav-h1"),

		"site" => array("text"=>"Site", "title"=>"Configure and define site related items", "class"=>"nav-h2 nolink"),
		"changepwd" => array("text"=>"change password", "url"=>"?p=changepwd", "title"=>"Change the administrator password"),
		"siteurl" => array("text"=>"site link", "url"=>"?p=siteurl", "title"=>"Set the main link to the site"),
		"meta" => array("text"=>"meta",  "url"=>"?p=meta", "title"=>"Set default meta tags to enhace search enginge visibility"),
		"tracker" => array("text"=>"tracker",  "url"=>"?p=tracker", "title"=>"Track site using Google Analytics"),
		"htmlparts" => array("text"=>"htmlparts", "url"=>"?p=htmlparts", "title"=>"Change htmlparts of site, including header and footer"),
		"navigation" => array("text"=>"navigation", "url"=>"?p=navigation", "title"=>"Define the site navigation menus, including your own navigational menus"),
		"stylesheet" => array("text"=>"stylesheet", "url"=>"?p=stylesheet", "title"=>"Set and edit the stylesheet"),
		"debug" => array("text"=>"debug", "url"=>"?p=debug", "title"=>"Print out debug information and current configuration"),

		"addons" => array("text"=>"Addons", "title"=>"Install, update and configure addons", "class"=>"nav-h2 nolink"),
		"fileupload" => array("text"=>"fileupload", "url"=>"?p=fileupload", "title"=>"Upload files and images"),

//		"other" => array("text"=>"Other", "title"=>"Other things, to be removed?", "class"=>"nav-h2 nolink"),
//		"header" => array("text"=>"header", "url"=>"?p=header", "title"=>"Define the header and logo of the site"),
//		"footer" => array("text"=>"footer", "url"=>"?p=footer", "title"=>"Define the footer of the site"),
//		"relatedsites" => array("text"=>"related sites", "url"=>"?p=relatedsites", "title"=>"Use and define related sites"),
//		"navbar" => array("text"=>"navigation bar", "url"=>"?p=navbar", "title"=>"Define the navigation bar (main menu) of the site"),
	);

	protected static $pages = array(
		"home" => array("file"=>"home.php", "title"=>"Home of admin area"),
		"changepwd" => array("file"=>"changepwd.php", "title"=>"Admin area: change password"),
		"siteurl" => array("file"=>"siteurl.php", "title"=>"Admin area: set sitelink"),
		"meta" => array("file"=>"meta.php", "title"=>"Admin area: set meta information"),
		"tracker" => array("file"=>"tracker.php", "title"=>"Admin area: enable tracking using Google Analytics"),
		"htmlparts" => array("file"=>"htmlparts.php", "title"=>"Admin area: edit htmlparts of the site"),
//		"header" => array("file"=>"header.php", "title"=>"Admin area: define the header of the site"),
//		"footer" => array("file"=>"footer.php", "title"=>"Admin area: define the footer of the site"),
		"navigation" => array("file"=>"navigation.php", "title"=>"Admin area: define and set navigation menus"),
//		"relatedsites" => array("file"=>"relatedsites.php", "title"=>"Admin area: use and define related sites"),
//		"navbar" => array("file"=>"navbar.php", "title"=>"Admin area: set navigation bar, the main menu"),
		"stylesheet" => array("file"=>"stylesheet.php", "title"=>"Admin area: set and edit the stylesheet"),
		"fileupload" => array("file"=>"fileupload.php", "title"=>"Admin area: upload files and images"),
		"debug" => array("file"=>"debug.php", "title"=>"Admin area: print out debug and config information"),
	);


	// ------------------------------------------------------------------------------------
	//
	// Public internal variables
	//
	

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
	// Frontcontroller. Redirect to choosen page and return the resulting html. 
	//
	public static function DoIt() {
		
		$pp = CPrinceOfPersia::GetInstance();
		
		// Check and get the current page referer
		$p = isset($_GET['p']) && array_key_exists($_GET['p'], self::$pages) ? $_GET['p'] : 'home'; 		
		
		// Set the current menu choice to active
		self::$menu[$p]['active'] = 'active';

		// Prepare the html for the page
		$pp->pageTitle = self::$pages[$p]['title'];
		$sidemenu = CNavigation::GenerateMenu(self::$menu, false, 'sidemenu');
		
		// Process the actual page and fill in $page
		require(dirname(__FILE__) . "/" . self::$pages[$p]['file']);

		// Create the resulting page
		$html = <<<EOD
<article class="span-18 colborder">
	{$page}
</article>	
<aside class="span-5 last">
	{$sidemenu}
</aside>
EOD;

		return $html;
	}


	/**
	 * Check the syntax of some PHP code.
	 * @param string $code PHP code to check.
	 * @return boolean|array If false, then check was successful, otherwise an array(message,line) of errors is returned.
	 */
/*	public static function CheckPHPSyntaxError($code) {
			$braces=0;
			$inString=0;
			foreach (token_get_all('<?php ' . $code) as $token) {
					if (is_array($token)) {
							switch ($token[0]) {
									case T_CURLY_OPEN:
									case T_DOLLAR_OPEN_CURLY_BRACES:
									case T_START_HEREDOC: ++$inString; break;
									case T_END_HEREDOC:   --$inString; break;
							}
					} else if ($inString & 1) {
							switch ($token) {
									case '`': case '\'':
									case '"': --$inString; break;
							}
					} else {
							switch ($token) {
									case '`': case '\'':
									case '"': ++$inString; break;
									case '{': ++$braces; break;
									case '}':
											if ($inString) {
													--$inString;
											} else {
													--$braces;
													if ($braces < 0) break 2;
											}
											break;
							}
					}
			}
			$inString = @ini_set('log_errors', false);
			$token = @ini_set('display_errors', true);
			ob_start();
			$braces || $code = "if(0){{$code}\n}";
			if (eval($code) === false) {
					if ($braces) {
							$braces = PHP_INT_MAX;
					} else {
							false !== strpos($code,CR) && $code = strtr(str_replace(CRLF,LF,$code),CR,LF);
							$braces = substr_count($code,LF);
					}
					$code = ob_get_clean();
					$code = strip_tags($code);
					if (preg_match("'syntax error, (.+) in .+ on line \d+)$'s", $code, $code)) {
							$code[2] = (int) $code[2];
							$code = $code[2] <= $braces
									? array($code[1], $code[2])
									: array('unexpected $end' . substr($code[1], 14), $braces);
					} else $code = array('syntax error', 0);
			} else {
					ob_end_clean();
					$code = false;
			}
			@ini_set('display_errors', $token);
			@ini_set('log_errors', $inString);
			return $code;
	}
*/

}


/**
*    Check Syntax
*    Performs a Syntax check within a php script, without killing the parser (hopefully)
*    Do not use this with PHP 5 <= PHP 5.0.4, or rename this function.
*
*    @params    string    PHP to be evaluated
*    @return    array    Parse error info or true for success
**/
function php_check_syntax( $php, $isFile=false )
{
    # Get the string tokens
    $tokens = token_get_all( '<?php '.trim( $php  ));
   
    # Drop our manually entered opening tag
    array_shift( $tokens );
    token_fix( $tokens );

    # Check to see how we need to proceed
    # prepare the string for parsing
    if( isset( $tokens[0][0] ) && $tokens[0][0] === T_OPEN_TAG )
       $evalStr = $php;
    else
        $evalStr = "<?php\n{$php}?>";

    if( $isFile OR ( $tf = tempnam( NULL, 'parse-' ) AND file_put_contents( $tf, $php ) !== FALSE ) AND $tf = $php )
    {
        # Prevent output
        ob_start();
        system( 'C:\inetpub\PHP\5.2.6\php -c "'.dirname(__FILE__).'/php.ini" -l < '.$php, $ret );
        $output = ob_get_clean();

        if( $ret !== 0 )
        {
            # Parse error to report?
            if( (bool)preg_match( '/Parse error:\s*syntax error,(.+?)\s+in\s+.+?\s*line\s+(\d+)/', $output, $match ) )
            {
                return array(
                    'line'    =>    (int)$match[2],
                    'msg'    =>    $match[1]
                );
            }
        }
        return true;
    }
    return false;
}

//fixes related bugs: 29761, 34782 => token_get_all returns <?php NOT as T_OPEN_TAG
function token_fix( &$tokens ) {
    if (!is_array($tokens) || (count($tokens)<2)) {
        return;
    }
   //return of no fixing needed
    if (is_array($tokens[0]) && (($tokens[0][0]==T_OPEN_TAG) || ($tokens[0][0]==T_OPEN_TAG_WITH_ECHO)) ) {
        return;
    }
    //continue
    $p1 = (is_array($tokens[0])?$tokens[0][1]:$tokens[0]);
    $p2 = (is_array($tokens[1])?$tokens[1][1]:$tokens[1]);
    $p3 = '';

    if (($p1.$p2 == '<?') || ($p1.$p2 == '<%')) {
        $type = ($p2=='?')?T_OPEN_TAG:T_OPEN_TAG_WITH_ECHO;
        $del = 2;
        //update token type for 3rd part?
        if (count($tokens)>2) {
            $p3 = is_array($tokens[2])?$tokens[2][1]:$tokens[2];
            $del = (($p3=='php') || ($p3=='='))?3:2;
            $type = ($p3=='=')?T_OPEN_TAG_WITH_ECHO:$type;
        }
        //rebuild erroneous token
        $temp = array($type, $p1.$p2.$p3);
        if (version_compare(phpversion(), '5.2.2', '<' )===false)
            $temp[] = isset($tokens[0][2])?$tokens[0][2]:'unknown';

        //rebuild
        $tokens[1] = '';
        if ($del==3) $tokens[2]='';
        $tokens[0] = $temp;
    }
    return;
}