<?php 
require_once("../config.php");
$pp->pageTitle = "Install medes";

$page = CInstall::DoIt();
$pp->PrintHTMLPage($page);
