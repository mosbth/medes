<?php 
require_once("config.php");
$pp->pageTitle = "Template";

// Use CArticle to get content into page
//$article = CArticle::DoIt();
//$article1 = CArticle::Get('moped');
//$article2 = CArticle::Get('mask');


$page = <<<EOD
<article>
<h1>Create a page using template.php</h1>
<p>Copy this file, save it with a new name and edit its content to create a new webpage.
</article>
EOD;

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php");