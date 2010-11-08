<?php
// ===========================================================================================
//
// config.php (config-sample.php)
//
// General configurations for the site. Copy config-sample.php to config.php and edit it.
// All configurations are hold in the $cfg-object and can be used all over the site.
//

// Enable auto-load of class declarations
function __autoload($aClassName){require_once(dirname(__FILE__)."/src/{$aClassName}/{$aClassName}.php");}

// -------------------------------------------------------------------------------------------
//
// The config-object, $cfg
//
// Create a $cfg-object for this site and and set its values
//
$pp = CPrinceOfPersia::GetInstance();

