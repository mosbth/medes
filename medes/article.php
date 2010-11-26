<?php 
require_once("config.php");
$pp->pageTitle = "Article";
$page = CArticle::DoIt();

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
