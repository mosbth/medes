<?php 
require_once("../../config.php");
$pp->pageTitle = "Example on how to use CRSSFeed";

/*
// Check whats needed to use this class
$extNeeded = array('xml', 'pcre');
$extRecommended = array('mbstring', 'iconv', 'curl', 'zlib');
foreach($extNeeded as $val) {
	if (!extension_loaded($val)) {
		echo "<p>missing needed {$val} extension.";
	}
}
foreach($extRecommended as $val) {
	if (!extension_loaded($val)) {
//		echo "<p>missing recommended {$val} extension.";
	}
}	
*/

$feed = new RSSFeed();
$feed->SetChannel(
	'http://www.mysite.com/xml.rss',
	'My feed name',
	'My feed description',
	'en-us',
	'My copyright text',
	'me',
	'my subject');
$feed->SetImage('http://www.mysite.com/mylogo.jpg');
$feed->SetItem('http://www.mysite.com/article.php?id=bla',
               'name',
               'description');
$feed->SetItem('http://www.mysite.com/article.php?id=bla',
               'name',
               'description');

// and store to file
$page = $myfeed->Output();


include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
