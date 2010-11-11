<?php

$check = "";
// ------------------------------------------------------------------------------
//
// Check that the data-directory is writable
//
$result = "";
if(is_writable(dirname(__FILE__) . "/../../data/")) {
	$result = "<span style='color:green'>[OK]</span>";
} else {
	$result = "<span style='color:red'>[FAILED]</span>. Create the directory and chmod to make it writable by the webserver.";
}
$check .= "<p>The directory <code>medes/data</code> is writable by the webserver?<br>{$result}";


// ------------------------------------------------------------------------------
//
// Check if the config file exists and is writable. If it exists then exit the procedure.
//

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


// Save sitelink, reload page and it should work, if not...


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Do a fresh installation of medes</h1>
<!-- <h1>Do a fresh (re-)installation of medes</h1> -->
<h2>Do some initial checks</h2>
{$check}
<h2>Ready to proceed?</h2>
<p>If it looks okey then proceed to the admin area to set the admin password and start configuring
your medes website.</p>
<p><a href="adm.php?p=changepwd">Admin area: change password</a>.</p>
<p>You can always run this procedure again by by pointing the browser to <code>medes/install.php</code>.
EOD;

