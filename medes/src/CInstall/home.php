<?php

$pp = CPrinceOfPersia::GetInstance();
$pp->pageInlineStyle .= <<<EOD
span.ok{color:green;text-transform:uppercase;}
span.fail{color:red;text-transform:uppercase;}
p.fix{padding-left:3em;}
EOD;

$check = "";

// ------------------------------------------------------------------------------
//
// Create default settings for configuration
//
$config['header'] 		= file_get_contents(dirname(__FILE__) . "data/default/header.php");
$config['footer'] 		= file_get_contents(dirname(__FILE__) . "data/default/footer.php");
$config['stylesheet'] = 'stylesheet_compatibility.css';


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
	$result = "Create the directory and chmod to make it writable by the webserver.";
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
$case 	= "Fresh install without an existing config-file <code>medes/data/CPrinceOfPersia_config.php</code>.";
$class 	= "ok";
$result = "";
if(is_readable(dirname(__FILE__) . "/../../data/CPrinceOfPersia_config.php")) {
	$result = "A config-file already exists. Remove it 'by hand' before doing a fresh installation.";
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
$case 	= "Setting the sitelink to this website (starting from the root of this server).";
$class 	= "ok";
$result = "";
$siteUrl = substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - strlen("medes/install.php"));
$pp->config['siteurl'] = $siteUrl;

if($dataDirectoryIsWritable) {
	$pp->UpdateConfiguration(array('siteurl'=>$siteUrl));
	$result = "Sitelink = {$siteUrl}";
} else {
	$class 	= "fail";
	$result = "The data-directory is not writable and the configuration can not be stored.";
}

$check .= <<<EOD
<p>
<span class={$class}>[{$class}]</span> 
{$case}
<p class=fix><em>{$result}</em></p>
EOD;


// Save sitelink, reload page and it should work, if not...


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Do a fresh installation of medes</h1>
<!-- <h1>Do a fresh (re-)installation of medes</h1> -->
<h2>Checking the environment</h2>
{$check}
<h2>Done</h2>
<p>Proceed to the admin area to set the admin password and start configuring
your medes website.</p>
<p><a href="adm.php?p=changepwd">Admin area: change password</a>.</p>
<p>You can always run this procedure again by by pointing the browser to <code>medes/install.php</code>.
EOD;

