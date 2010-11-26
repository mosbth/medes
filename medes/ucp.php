<?php 
require_once("config.php");
$page = CUserControlPanel::DoIt();

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
