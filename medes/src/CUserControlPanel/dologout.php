<?php

// ------------------------------------------------------------------------------
//
// Destroy session and redirect to logout-page
//
require_once(dirname(__FILE__) . "/destroysession.php");
header("Location: " . $pp->PrependWithSiteUrl($_SERVER['PHP_SELF'] . "?p=logout"));
