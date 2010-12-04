<?php 
require_once("../../config.php");
$pp->pageTitle = "Example on how to use CRSSFeed";

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

// get some feeds
$feed1 = CRSSFeed::GetContent("http://newsrss.bbc.co.uk/rss/newsonline_world_edition/front_page/rss.xml", 5);
$feed2 = CRSSFeed::GetContent("http://www.bth.se/info/aktuellt.nsf/rss", 5);
$feed3 = CRSSFeed::GetContent("http://db-o-webb.blogspot.com/feeds/posts/default?alt=rss", 5);
$feed4 = CRSSFeed::GetContent("http://picasaweb.google.com/data/feed/base/user/programvaruteknik?alt=rss&kind=album&hl=sv", 5);
$feed5 = CRSSFeed::GetContent("http://twitter.com/favorites/70333468.rss", 5);
$feed6 = CRSSFeed::GetContent("http://gdata.youtube.com/feeds/base/users/mosdbwebb/uploads?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile", 5);
$feed7 = CRSSFeed::GetContent("http://github.com/mosbth.atom", 5);
$feed8 = CRSSFeed::GetContent("http://internetworld.idg.se/rss/nyheterinternetworld", 5);

$page = <<<EOD
<div class=span-6>{$feed1}</div>
<div class=span-6>{$feed2}</div>
<div class=span-6>{$feed3}</div>
<div class="span-6 last">{$feed4}</div>
<div class=span-6>{$feed5}</div>
<div class=span-6>{$feed6}</div>
<div class=span-6>{$feed7}</div>
<div class="span-6 last">{$feed8}</div>

EOD;

include($pp->medesPath . "/inc/header.php");
echo $page;
include($pp->medesPath . "/inc/footer.php"); 
