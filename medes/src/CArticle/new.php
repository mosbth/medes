<?php
$page = '<form method=post action="?p=edit">';
foreach(self::$attribute as $key=>$value)
  $page .= $key.': <textarea name='.$key.'></textarea><br>';
$page .= "<input type=submit name=doSaveArticle value=Submit></form>";
