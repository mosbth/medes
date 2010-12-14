<?php 
require_once("../../config.php");
$pp->pageTitle = "Example on how to use the standard module CBlog";

$blog = new CBlog("blog-example");

$page = "<h1>Example on how to use CBlog</h1><p><a href='?add'>Click here to create some more sample posts.</a>";

// Add some sample posts
if(isset($_GET['add'])) {
	$blog->AddPosts(
		array(
			array(
				"title"=>"Title  of post #1", 
				"article"=>"This is the first blog-post.", 
				"author"=>"Mikael Roos", 
			),
			array(
				"title"=>"Title  of post #2", 
				"article"=>"This is the first blog-post.", 
				"author"=>"Mikael Roos", 
			),
			array(
				"title"=>"Title  of post #3", 
				"article"=>"This is the first blog-post.", 
				"author"=>"Mikael Roos", 
			),
		)
	);
}

$posts = $blog->GetPosts();
foreach($posts as $val) {
	$page .= "<h2>{$val['title']}</h2><p>{$val['created']}<p>{$val['article']}<p>By {$val['author']}";
}

$page .= <<<EOD


EOD;

$pp->PrintHTMLPage($page);
