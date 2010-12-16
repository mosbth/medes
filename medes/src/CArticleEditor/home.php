<?php
//$articles = $CArticle->GetArticles(array('rowid, *'));
//
//$page = '';
//
//$page .= '<ol>';
//foreach($articles as $row){
//$page .= "<li><a href=\"?p=edit&id={$row['rowid']}\">{$row['title']}</a></li>";
//}
//$page .= '</ol>';

$page = <<<EOD
<h1>Article Editor</h1>
<p>Here you edit articles.</p>
EOD;
