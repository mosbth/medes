<?php
// create the article table with all the attributes of the $attribute array + created, modified and deleted datetimes
$q = 'create table if not exists article(';
foreach(CArticle::$attribute as $key=>$value)
  $q .= $key.' '.$value['type'].',';
$q .= 'created datetime, modified datetime, deleted datetime);';
$stmt = CArticle::$db->prepare($q);
$page = "<p>Executing install of articles db: <br><pre>".$stmt->queryString."</pre>";
$stmt->execute();

// insert a sample home-article
$q = 'insert into article values(';
$q .= '"home", "admin", "&copy;2k10, all rights reserved", "description", "home, phpmedes", "<p>Welcome to phpmedes!</p>", datetime("now"), null, null);';
$stmt = CArticle::$db->prepare($q);
$page .= "<p>Executing install of articles db: <br><pre>".$stmt->queryString."</pre>";
$stmt->execute();
