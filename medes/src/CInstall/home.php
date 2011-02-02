<?php
$pp->pageStyle .= <<<EOD
span.ok{color:green;text-transform:capitalize;}
span.fail{color:red;text-transform:uppercase;}
span.info{color:#205791;text-transform:capitalize;background:transparent;border:none;padding:0;}
p.fix{padding-left:3em;}
EOD;

$check = "";


// ------------------------------------------------------------------------------
//
// Check that the data-directory is writable
//
$case 	= "The directory <code>medes/data</code> exists and is writable by the webserver.";
$class 	= "ok";
$result = "";
$dataDirectoryIsWritable = true;
if(!is_dir(dirname(__FILE__) . "/../../data/")) {
	$dataDirectoryIsWritable = false;
	$result = "Create the directory and make it writable (for example chmod 777) by the webserver.";
	$class = "fail";
} else if(!is_writable(dirname(__FILE__) . "/../../data/")) {
	$dataDirectoryIsWritable = false;
	$result = "Make the directory writable (for example chmod 777) by the webserver.";
	$class = "fail";
} 
$check .= <<<EOD
<p>
<span class="{$class}">[{$class}]</span> 
{$case}
<p class="fix"><em>{$result}</em></p>
EOD;


// ------------------------------------------------------------------------------
//
// Install the database
//
if($dataDirectoryIsWritable) {
	$case 	= "Creating and initiating the database <code>medes/data/CDatabaseController.db</code>.";
	$class 	= "ok";
	$result = "";
	$databaseExists = false;
	if(is_readable(dirname(__FILE__) . "/../../data/CDatabaseController.db")) {
		$databaseExists = true;
		$result = "A database already exists. Leaving it as is. Remove it 'by hand', if needed.";
		$class = "info";
	} else {
		$a = new CArticle();
		$a->Install();
		$a = new CContentPage();
		$a->Install();
	}
	$check .= <<<EOD
<hr>
<h2>Installing</h2>
<p>
<span class="{$class}">[{$class}]</span> 
{$case}
<p class="fix"><em>{$result}</em></p>
EOD;
}


// ------------------------------------------------------------------------------
//
// Check if the config file exists and is writable. 
//
if($dataDirectoryIsWritable) {
	$case 	= "Fresh install, writing the config-file to <code>medes/data/CPrinceOfPersia_config.php</code>.";
	$class 	= "ok";
	$result = "";
	$configFileExists = false;
	if(is_readable(dirname(__FILE__) . "/../../data/CPrinceOfPersia_config.php")) {
		$configFileExists = true;
		$result = "A config-file already exists. Remove it 'by hand' to do a fresh installation.";
		$class = "info";
	} 
	$check .= <<<EOD
<p>
<span class="{$class}">[{$class}]</span> 
{$case}
<p class="fix"><em>{$result}</em></p>
EOD;
}

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
$siteUrl = substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - strlen("medes/page/install.php"));
$pp->config['siteurl'] = $siteUrl;

if($dataDirectoryIsWritable && !$configFileExists) {
	$pp->UpdateConfiguration(array('siteurl'=>$siteUrl));
	$result = "Sitelink = {$siteUrl}";
	$check .= <<<EOD
	<p>
	<span class="{$class}">[{$class}]</span> 
	{$case}
	<p class="fix"><em>{$result}</em></p>
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
			"1"=>array("text"=>"home", "url"=>"medes/page/template.php", "title"=>"A default template page to start with"),
			"2"=>array("text"=>"page", "url"=>"medes/page/page.php?p=template-page", "title"=>"A template page that stores content in the database"),
			"3"=>array("text"=>"acp", "url"=>"medes/page/acp.php", "title"=>"Administrate and configure the site and its addons"),
			"4"=>array("text"=>"ucp", "url"=>"medes/page/ucp.php", "title"=>"User control panel"),
			"5"=>array("text"=>"install", "url"=>"medes/page/install.php", "title"=>"Install"),
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

$done = "<p><strong><span class=fail>[fail]</span> A fresh installation of medes failed. Correct the errors above and <a href=''>reload this page</a>.</strong></p>";
if($dataDirectoryIsWritable && !$configFileExists) {
	$pp->UpdateConfiguration($config);
	$done = <<<EOD
<hr>
<h2>Installation complete</h2>
<p>Proceed to the admin area to set the admin password and start configuring
your medes website.</p>
<p><a href="acp.php?p=changepwd">Admin area: change password</a></p>
<p>You can always run this procedure again by by pointing the browser to <code>medes/install.php</code>.
All dynamic site data is included in the data-directory, <code>medes/data</code>, always consider to make a backup of its content.</p>
	
EOD;
} else {
	unset($config['navigation']['navbar']['nav'][1]);
	unset($config['navigation']['navbar']['nav'][2]);
	unset($config['navigation']['navbar']['nav'][3]);
	unset($config['navigation']['navbar']['nav'][4]);
	$pp->UpdateConfiguration($config, false);
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<article id="install" class="border span-18 ">
<h1>Do a fresh installation of medes</h1>
<hr>
<!-- <h1>Do a fresh (re-)installation of medes</h1> -->
<h2>Information</h2>
<p>This scripts installs medes. Correct any error and <a href=''>reload this page</a> until all is green.</p>
<p>There is currently no nice way to do an upgrade of an existing installation, however, running this script on
an existing installation, makes no harm.</p>
<hr>

<h2>Checking environment</h2>
{$check}
{$done}
<hr>
</article>
EOD;

