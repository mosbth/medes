<?php 
require_once("../../config.php");
$pp->pageTitle = "Example on how to use the standard module CBlog";

$page = CBlogControlPanel::DoIt();

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
