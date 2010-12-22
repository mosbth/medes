<?php 
require_once("../config.php");
$pp->pageTitle = "Template";

$page = <<<EOD
<article>
<h1>Create a page using template.php</h1>
<p>Copy this file, <code>medes/page/template.php</code>, to create a new webpage.
Use an editor of your choice to change its content.
</p>
</article>
EOD;

$pp->PrintHTMLPage($page);
