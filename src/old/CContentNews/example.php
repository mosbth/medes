<?php 
require_once("../../config.php");
$pp->pageTitle = "Example on how to use the standard module CNews";

$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
$news = new CNews("news-example");

$page = <<<EOD
<h1>Example on how to use CNews</h1>
<p>
<a href='?view'>View all.</a>
<a href='?add'>Create a article.</a>
<a href='?deleteall'>Delete all articles.</a>
<a href='?viewdeleted'>View all deleted (wastebasket).</a>
</p>
EOD;


// ------------------------------------------------------------------------------------
//
// Add some sample news articles
//
if(isset($_GET['add'])) {
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"), $_SERVER['PHP_SELF']);		
	} 
	
	$news->AddArticle(
		array(
			array(
				"title"=>"Title  of news article", 
				"article"=>"This is the content of the news article.", 
				"author"=>$pp->uc->GetAccountName(),
			),
		)
	);
	$pp->ReloadPageAndRemember(array("output"=>"A new article is created.", "output-type"=>"success"), $_SERVER['PHP_SELF']);
}


// ------------------------------------------------------------------------------------
//
// Delete all news articles
//
else if(isset($_GET['deleteall'])) {
	$news->DeleteAllArticles();
	$pp->ReloadPageAndRemember(array("output"=>"All articles are deleted to the wastebasket.", "output-type"=>"success"), $_SERVER['PHP_SELF']);
}


// ------------------------------------------------------------------------------------
//
// Get all news articles
//
else if(isset($_GET['view'])) {
	$articles = $news->GetArticles();
	foreach($articles as $val) {
		$page .= "<h2>{$val['title']}</h2><p>{$val['created']}<p>{$val['article']}<p>By {$val['author']}";
	}
}

$page .= <<<EOD
<p><output class={$remember['output-type']}>{$remember['output']}</output></p>
EOD;

$pp->PrintHTMLPage($page);
