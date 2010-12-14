<?php 
require_once("../../config.php");
$pp->pageTitle = "Article";
$page = CArticleEditor::DoIt();

$pp->PrintHTMLPage($page);
