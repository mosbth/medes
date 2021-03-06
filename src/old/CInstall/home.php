<?php
$pp->pageStyle .= <<<EOD
span.ok{color:green;text-transform:capitalize;}
span.fail{color:red;text-transform:uppercase;}
span.info{color:#205791;text-transform:capitalize;background:transparent;border:none;padding:0;}
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
$result = empty($result) ? "" : "<p class='info'>{$result}</p>";
$check .= <<<EOD
<p>
<span class="{$class}">[{$class}]</span> 
{$case}
{$result}
EOD;


// ------------------------------------------------------------------------------
//
// Install the database
//
$databaseExists = false;
if($dataDirectoryIsWritable) {
	$case 	= "Creating and initiating the database <code>medes/data/CDatabaseController.db</code>.";
	$class 	= "ok";
	$result = "";
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
	$result = empty($result) ? "" : "<p class='info'>{$result}</p>";
	$check .= <<<EOD
<h2>Installing</h2>
<p>
<span class="{$class}">[{$class}]</span> 
{$case}
{$result}
EOD;
}


// ------------------------------------------------------------------------------
//
// Check if the config file exists and is writable. 
//
$configFileExists = false;
if($dataDirectoryIsWritable) {
	$case 	= "Fresh install, writing the config-file to <code>medes/data/CPrinceOfPersia_config.php</code>.";
	$class 	= "ok";
	$result = "";
	if(is_readable(dirname(__FILE__) . "/../../data/CPrinceOfPersia_config.php")) {
		$configFileExists = true;
		$result = "A config-file already exists. Remove it 'by hand' to do a fresh installation.";
		$class = "info";
	} 
	$result = empty($result) ? "" : "<p class='info'>{$result}</p>";
	$check .= <<<EOD
<p>
<span class="{$class}">[{$class}]</span> 
{$case}
{$result}
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
	$result = "<p class='info'>Sitelink = {$siteUrl}</p>";
	$check .= <<<EOD
<p>
<span class="{$class}">[{$class}]</span> 
{$case}
{$result}
EOD;
}


// ------------------------------------------------------------------------------
//
// Create default settings for configuration
//
$config['htmlparts-htmlhead'] 		= file_get_contents(dirname(__FILE__) . "/../CPrinceOfPersia/htmlparts/mds/html_head.php");
$config['htmlparts-pagetop'] 			= file_get_contents(dirname(__FILE__) . "/../CPrinceOfPersia/htmlparts/mds/page_top.php");
$config['htmlparts-pageheader'] 	= file_get_contents(dirname(__FILE__) . "/../CPrinceOfPersia/htmlparts/mds/page_header.php");
$config['htmlparts-pagecontent'] 	= file_get_contents(dirname(__FILE__) . "/../CPrinceOfPersia/htmlparts/mds/page_content.php");
$config['htmlparts-pagefooter'] 	= file_get_contents(dirname(__FILE__) . "/../CPrinceOfPersia/htmlparts/mds/page_footer.php");
$config['htmlparts-pagebottom'] 	= file_get_contents(dirname(__FILE__) . "/../CPrinceOfPersia/htmlparts/mds/page_bottom.php");

$config['navigation'] = array(
	"navbar"=>array(
		"text"=>"Main navigation bar",
		"nav"=>array(
			"1"=>array("text"=>"home", "url"=>"medes/page/template.php", "title"=>"A default template page to start with"),
			"2"=>array("text"=>"page", "url"=>"medes/page/page.php?p=template-page", "title"=>"A template page that stores content in the database"),
			"3"=>array("text"=>"columns", "url"=>"medes/page/column_layout.php", "title"=>"Example page to show how flexible column layout works"),
			"4"=>array("text"=>"acp", "url"=>"medes/page/acp.php", "title"=>"Administrate and configure the site and its addons"),
			"5"=>array("text"=>"ucp", "url"=>"medes/page/ucp.php", "title"=>"User control panel"),
			"6"=>array("text"=>"install", "url"=>"medes/page/install.php", "title"=>"Install"),
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

$done = "<p class='notice'><strong>A fresh installation of medes failed. The configuration of the site was not changed. Correct the errors above and <a href=''>reload this page</a>.</strong></p>";
if($dataDirectoryIsWritable && !$configFileExists) {
	$pp->UpdateConfiguration($config);
	$done = <<<EOD
<h2>Installation complete</h2>
<p>Proceed to the admin area to set the admin password and start configuring
your medes website.</p>
<p><a href="acp.php?p=changepwd">Admin area: change password</a></p>
<p>You can always run this procedure again by by pointing the browser to <code>medes/install.php</code>.
The sites dynamic data, config-files and file-databases, are all stored in the data-directory, <code>medes/data</code>. 
Always consider to make a proper backup of its content.</p>
	
EOD;
} else {
	if(!$configFileExists) {
		unset($config['navigation']['navbar']['nav'][1]);
		unset($config['navigation']['navbar']['nav'][2]);
		unset($config['navigation']['navbar']['nav'][3]);
		unset($config['navigation']['navbar']['nav'][4]);
		unset($config['navigation']['navbar']['nav'][5]);
	}
	$pp->UpdateConfiguration($config, false);
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<article id="install" class="border span-18 ">
<h1>Do a fresh installation of medes</h1>

<!-- <h1>Do a fresh (re-)installation of medes</h1> -->
<h2>Information</h2>
<p>This scripts installs medes. Correct any error and <a href=''>reload this page</a> until all is green.</p>
<p>There is currently no nice way to do an upgrade of an existing installation, however, running this script on
an existing installation, makes no harm.</p>

<h2>Checking environment</h2>
{$check}
{$done}
</article>

EOD;

