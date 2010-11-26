<?php 
require_once("config.php");
$pp->pageTitle = "Admin Control Panel";
$page = CAdminControlPanel::DoIt();

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
