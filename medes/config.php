<?php
// ===========================================================================================
//
// config.php (config-sample.php)
//
// Needed configurations for the site. Any page need to include this file as their first 
// action.
// All configurations are hold in the $pp-object and can be used all over the site.
//

// Enable auto-load of class declarations
function __autoload($aClassName) {
	$file = dirname(__FILE__)."/src/{$aClassName}/{$aClassName}.php";
	if(is_file($file)) {
		require_once($file);
	}
}

// -------------------------------------------------------------------------------------------
//
// The master of this site, $pp
//
$pp = CPrinceOfPersia::GetInstance();
