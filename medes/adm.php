<?php 
require_once("config.php");
$pp->pageTitle = "Admin Area";
$page = CAdminArea::DoIt();

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
