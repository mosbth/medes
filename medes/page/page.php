<?php 
require_once("../config.php");
$pp->pageTitle = "Template using CContentPage to store content in database";


// ------------------------------------------------------------------------------------
//
// Create a page with content stored in database
//
$cp = new CContentPage("template-page"); 

$content1 = <<<EOD
<article>
<h1>Template for content stored in database</h1>
<p>This is a article stored in the database. It makes use of an article key that makes it easy to 
store content in database.
</p>
<p>Copy this file, <code>medes/page/template_article.php</code>, save it with a new name and edit its content to create a new webpage.
</p>
</article>
EOD;

$cp->SaveContent($content1);


// Get the content
$page = $cp->GetPage();
/*
$content2 = $cp->GetContent();
$menu = $cp->GetMenu();
$page = <<<EOD
{$content2}
{$menu}
EOD;
*/

$pp->PrintHTMLPage($page);
