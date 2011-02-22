<?php 
require_once("../config.php");
$pp->pageTitle = "Testpage to show how the flexible column-layout works";

// Get dynamic page
$content = "<p>This is the main content of the page, content such as text, images, articles, forum or blogposts.
You can use the following links to hide or show sidebars together with the content.</p>
<p><a href='?sidebar1&amp;sidebar2'>Display both sidebars</a></p>
<p><a href='?'>Display no sidebar</a></p>
<p><a href='?sidebar1'>Display only the sidebar1</a></p>
<p><a href='?sidebar2'>Display only the sidebar2</a></p>
";
$sidebar1 = null;
$sidebar2 = null;

if(isset($_GET['sidebar1'])) {
	$sidebar1 = "<p>This is sidebar1.</p>";
}

if(isset($_GET['sidebar2'])) {
	$sidebar2 = "<p>This is sidebar2.</p>";
}

$pp->PrintHTMLPage($content, $sidebar1, $sidebar2);
