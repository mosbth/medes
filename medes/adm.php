<?php 
require_once("config.php");
$pp->pageTitle = "Siteadmin";
include($pp->medesPath . "/inc/header.php");
echo CAdminArea::DoIt();
include($pp->medesPath . "/inc/footer.php"); 
