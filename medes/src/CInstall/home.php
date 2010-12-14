<?php

//$pp = CPrinceOfPersia::GetInstance();
$pp->pageStyle .= <<<EOD
span.ok{color:green;text-transform:uppercase;}
span.fail{color:red;text-transform:uppercase;}
p.fix{padding-left:3em;}
EOD;

$check = "";


// ------------------------------------------------------------------------------
//
// Check that the data-directory is writable
//
$case 	= "The directory <code>medes/data</code> is writable by the webserver.";
$class 	= "ok";
$result = "";
$dataDirectoryIsWritable = true;
if(!is_writable(dirname(__FILE__) . "/../../data/")) {
	$dataDirectoryIsWritable = false;
	$result = "Make the directory writable (for example chmod 777) by the webserver.";
	$class = "fail";
} 
$check .= <<<EOD
<p>
<span class={$class}>[{$class}]</span> 
{$case}
<p class=fix><em>{$result}</em></p>
EOD;


// ------------------------------------------------------------------------------
//
// Check if the config file exists and is writable. If it exists then exit the procedure.
//
$case 	= "Fresh install, writing the config-file to <code>medes/data/CPrinceOfPersia_config.php</code>.";
$class 	= "ok";
$result = "";
$configFileExists = false;
if(is_readable(dirname(__FILE__) . "/../../data/CPrinceOfPersia_config.php")) {
	$configFileExists = true;
	$result = "A config-file already exists. Remove it 'by hand' before doing a fresh installation.";
	$class = "fail";
} 
$check .= <<<EOD
<h2>Installing</h2>
<p>
<span class={$class}>[{$class}]</span> 
{$case}
<p class=fix><em>{$result}</em></p>
EOD;


// ------------------------------------------------------------------------------
//
// Create a new config file, take a copy of an existing one
//

// ------------------------------------------------------------------------------
//
// Check the current version of medes and display the latest available versions.
// Provide link to download page.
//
//include files from phpmedes.org/latest_version.php, or readfile
//

// ------------------------------------------------------------------------------
//
// Find out the sitelink and display it. Enable to save it and redirect to admin and set admin password.
//
$case 	= "Setting the sitelink to this website (starting from the docroot of this webserver).";
$class 	= "ok";
$result = "";
$siteUrl = substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - strlen("medes/install.php"));
$pp->config['siteurl'] = $siteUrl;

if($dataDirectoryIsWritable && !$configFileExists) {
	$pp->UpdateConfiguration(array('siteurl'=>$siteUrl));
	$result = "Sitelink = {$siteUrl}";
	$check .= <<<EOD
	<p>
	<span class={$class}>[{$class}]</span> 
	{$case}
	<p class=fix><em>{$result}</em></p>
EOD;
}


// ------------------------------------------------------------------------------
//
// Create default settings for configuration
//
$config['header'] 		= file_get_contents(dirname(__FILE__) . "/default_site_header.php");
$config['footer'] 		= file_get_contents(dirname(__FILE__) . "/default_site_footer.php");
$config['navigation'] = array(
	"navbar"=>array(
		"text"=>"Main navigation bar",
		"nav"=>array(
			"1"=>array("text"=>"home", "url"=>"medes/template.php", "title"=>"A default template page to start with"),
			"2"=>array("text"=>"acp", "url"=>"medes/acp.php", "title"=>"Administrate and configure the site and its addons"),
			"3"=>array("text"=>"ucp", "url"=>"medes/ucp.php", "title"=>"User control panel"),
			"4"=>array("text"=>"article", "url"=>"medes/src/CArticle/example.php", "title"=>"CArticle"),
			"5"=>array("text"=>"article editor", "url"=>"medes/src/CArticleEditor/example.php", "title"=>"CArticleEditor"),
			"6"=>array("text"=>"blog", "url"=>"medes/src/CBlog/example.php", "title"=>"CBlog"),
			"7"=>array("text"=>"news", "url"=>"medes/src/CNews/example.php", "title"=>"CNews"),
			"8"=>array("text"=>"rss reader", "url"=>"medes/src/CRSSReader/example.php", "title"=>"CRSSReader"),
			"9"=>array("text"=>"install", "url"=>"medes/install.php", "title"=>"Install"),
		),
	),
	"relatedsites"=>array(
		"text"=>"Top left menu",
		"nav"=>array(
			"1"=>array("text"=>"phpmedes", "url"=>"http://phpmedes.org/", "title"=>"Home of phpmedes"),
			"2"=>array("text"=>"dbwebb", "url"=>"http://dbwebb.se/", "title"=>"Databases and Webb, it´s all about html, css, php and sql"),
		),
	),
);
$config['styletheme'] = array(
	"name"=>"core",
	"stylesheet"=>"screen_compatibility.css",
	"print"=>"print.css",
	"ie"=>"ie.css",
);
$config['meta'] = array(
	"author"=>"",
	"copyright"=>"",
	"description"=>"",
	"keywords"=>"",
);
$config['tracker'] = "";


$done = "<p><strong><span class=fail>[fail]</span> A fresh installation of medes failed. Correct the errors above and reload this page.</strong></p>";
if($dataDirectoryIsWritable && !$configFileExists) {
	$pp->UpdateConfiguration($config);
	$done = <<<EOD
<h2>Installation complete</h2>
<p>Proceed to the admin area to set the admin password and start configuring
your medes website.</p>
<p><a href="acp.php?p=changepwd">Admin area: change password</a></p>
<p>You can always run this procedure again by by pointing the browser to <code>medes/install.php</code>.
	
EOD;
} 


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Do a fresh installation of medes</h1>
<!-- <h1>Do a fresh (re-)installation of medes</h1> -->
<h2>Checking the environment</h2>
{$check}
{$done}
EOD;

