<?php 
require_once("config.php");
$pp->pageTitle = "Install medes";
$page = CInstall::DoIt();

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
