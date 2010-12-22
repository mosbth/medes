<?php 
require_once("../config.php");
$pp->pageTitle = "Template using CContentPage to store content in database";


// Get dynamic page
$cp = new CContentPage(); 
$page = $cp->ActionHandler();

$pp->PrintHTMLPage($page);
