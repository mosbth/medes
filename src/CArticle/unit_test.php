<?php 
require_once("../../config.php");
$pp->pageTitle = "Unit test of CArticle";


// ------------------------------------------------------------------------------------
//
// Create an instance of CArticle
//
$a = new CArticle();

$page = "<h1>Unit test of class CArticle</h1>";


// ------------------------------------------------------------------------------------
//
// Clear current and display content of current
//
$page .= "<h2>#Clear current and display content of current</h2>";
$a->ClearCurrent();
$page .= "<pre>" . var_export($a->current, true) . "</pre>";

$page .= var_export($a->GetId(), true);

$pp->PrintHTMLPage($page);
