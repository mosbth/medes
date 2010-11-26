<?php

// ------------------------------------------------------------------------------
//
// Logout and destroy the session
//
require_once(dirname(__FILE__) . "/destroysession.php");


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Logout</h1>
<p>You have now logged out. Welcome back another time.</p>
EOD;

