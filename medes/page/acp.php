<?php 
require_once("../config.php");
$pp->pageTitle = "Admin Control Panel";

$page = CAdminControlPanel::DoIt();
$pp->PrintHTMLPage($page);
