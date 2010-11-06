<?php 
require_once("config.php");
$pp->pageTitle = "Siteadmin";
include($pp->medesPath . "/inc/header.php");
CAdminArea::DoIt();
include($pp->medesPath . "/inc/footer.php"); 
