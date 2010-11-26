<?php
// generate a page from the article database
// if it doesn't exist, return 404
$stmt = self::$db->prepare('select title, author, copyright, description, keywords, article from article where rowid=:id');
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();
$row = $stmt->fetch();

$pp->pageTitle = $row['title'];
$pp->pageKeywords = $row['keywords'];
$pp->pageDescription = $row['description'];
$pp->pageAuthor = $row['author'];
$pp->pageCopyright = $row['copyright'];

$page = $row['article'];
if($page == '')
  header('HTTP/1.1 404 Not Found');
