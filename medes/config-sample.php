<?php
// ===========================================================================================
//
// config.php (config-sample.php)
//
// General configurations for the site. Copy config-sample.php to config.php and edit it.
// All configurations are hold in the $cfg-object and can be used all over the site.
//

// Enable auto-load of class declarations
function __autoload($aClassName){require_once(dirname(__FILE__)."/src/{$aClassName}.php");}

// -------------------------------------------------------------------------------------------
//
// The config-object, $cfg
//
// Create a $cfg-object for this site and and set its values
//
$cfg = CConfigSite::GetInstance();

// Set the link to this site. Can this be figured out dynamically?
// Leave empty-string if site is on top of website
$cfg->siteUrl = "/medes";


// -------------------------------------------------------------------------------------------
//
// Default meta-tags
//
// Set the default meta tags for this site. Each page can override them by setting individual 
// values in the following variables:
//  $cfg->pageKeywords
//  $cfg->pageDescription
//  $cfg->pageAuthor 
//  $cfg->pageCopyright
//
// Review the file inc/header.php to see how the variables are used.
//
$cfg->pageKeywords 		= 'General keywords for this site/page';
$cfg->pageDescription	= 'General description for this site/page';
$cfg->pageAuthor 			= 'Author of this site/page';
$cfg->pageCopyright 	= 'Copyright for this site/page';


// -------------------------------------------------------------------------------------------
//
// Google Analytics
//
// Are you using Google Analytics to track visits of this site? Then put the Javascript here.
// It will be output just before the </head>-tag in the file inc/header.php.
// 
$cfg->googleAnalytics = ''; // An empty string will disable this feature.


