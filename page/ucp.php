<?php 
require_once("../config.php");
$pp->pageTitle = "User Control Panel";

$page = CUserControlPanel::DoIt();
$pp->PrintHTMLPage($page);
