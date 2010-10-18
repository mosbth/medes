<?php
// ============================================================================================
//
// General functions and helpers.
//
// History:
// 2010-10-18 Created.
//


// --------------------------------------------------------------------------------------------
//
// FUNCTION
// Create link to current page
//
function createLinkToCurrentPage() {
	$uri = "http";
	$uri .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
	$uri .= "://";
	$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
	(($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
	$uri .= $_SERVER["SERVER_NAME"] . $serverPort . $_SERVER["REQUEST_URI"];
	return $uri;
}

?>