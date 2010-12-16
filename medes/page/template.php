<?php 
require_once("../config.php");
$pp->pageTitle = "Template";

$page = <<<EOD
<article>
<h1>Create a page using template.php</h1>
<p>Copy this file, save it with a new name and edit its content to create a new webpage.
</article>
EOD;

$pp->PrintHTMLPage($page);
