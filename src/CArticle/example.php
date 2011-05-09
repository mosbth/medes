<?php 
require_once("../../config.php");
$pp->pageTitle = "Example on how to use CArticle";


// ------------------------------------------------------------------------------------
//
// Create an instance of CArticle
//
$a = new CArticle();



$page = "";
$pp->PrintHTMLPage($page);

/*
$article->Install();
$page = "<h1>Example on using CArticle</h1>";
$owner = "CArticle_demo";


// ------------------------------------------------------------------------------------
//
// Delete all articles with the demo owner
//public function GetArticles($attributes=array('*'), $order=array(), $range=array('limit'=>10), $where=array()){
//
$articles = $article->GetArticles(array('rowid'), array(), array(), array(""=>"owner='{$owner}'", "AND"=>"deleted IS NULL"));
$page .= "<h2>Delete articles</h2><p>Deleting article with id = ";
foreach($articles as $key=>$val) {
	$page .= "{$val['rowid']} ";
	$article->Delete($val['rowid']);
}


// ------------------------------------------------------------------------------------
//
// Create a basic set of articles and display them
//
$page .= "<h2>Creating a new set of basic articles</h2>";

$content['title'] = "An article about Mumintrollet";
$content['author'] = "Mikael Roos";
$content['article'] = "This is the article text";
$content['owner'] = $owner;
$article->SaveNew($content);

$content['title'] = "Another article about Mumintrollet";
$article->SaveNew($content);

$content['title'] = "A third article about Mumintrollet";
$article->SaveNew($content);

// Get and display the content of the articels
$articles = $article->GetArticles(array('*'), array(), array(), array(""=>"owner='{$owner}'", "AND"=>"deleted IS NULL"));
//$articles = $article->GetArticles();
foreach($articles as $key=>$val) {
	$page .= "<p>Title: {$val['title']}<br>Content: {$val['article']}</p>";
}


// ------------------------------------------------------------------------------------
//
// Create a article using a unique key
//
$page .= "<h2>Creating an article with unique key</h2>";
$key = "demo-article-with-key";
$content['title'] = "An article with a key";
$content['key'] = $key;
$content['article'] = "This article has a key: {$key} which makes it easy to always get hold of and to update its content.";
$article->SaveByKey($content);

$item = $article->GetByKey($key);
$page .= "<p>First try with keyed article<p>Title: {$item->title}<br>Content: {$item->article}<br>Key: {$item->key}</p>";

$content['article'] .= " Adding some content to keyed article.";
$article->SaveByKey($content);

$content = $article->GetByKey($key);
$page .= "<p>Updating keyed article<p>Title: {$content->title}<br>Content: {$content->article}<br>Key: {$item->key}</p>";




//$article->Delete(26); 
// Create a basic set of articles and display them
/*
$content['title'] = "An article about Mumintrollet";
$content['author'] = "Mikael Roos";
$content['copyright'] = null;
$content['description'] = null;
$content['keywords'] = null;
$content['article'] = "<p>This is the article with text</p>";
$content['owner'] = "demo";
$content['key'] = "article1";
$article->SaveNew($content);

$content['title'] = "Another article about Mumintrollet";
$content['key'] = "article2";
$article->SaveNew($content);

$content['title'] = "A third article about Mumintrollet";
$content['key'] = "article3";
$article->SaveNew($content);


echo "<pre>" . var_dump($articles) . "</pre>";
*/

