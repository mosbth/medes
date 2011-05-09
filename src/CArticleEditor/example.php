<?php 
require_once("../../config.php");
$pp->pageTitle = "Example usage of ArticleEditor";
$page = CArticleEditor::DoIt();

$pp->PrintHTMLPage($page);
