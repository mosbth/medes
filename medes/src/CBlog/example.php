<?php 
require_once("config.php");
$pp->pageTitle = "Blog Control Panel";
$page = CBlogControlPanel::DoIt();

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
