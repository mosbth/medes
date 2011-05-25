<?php

$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>'', 'id'=>''));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
$id = isset($_POST['id']) ? $_POST['id'] : $remember['articleId'];

// ------------------------------------------------------------------------------
//
// Save article
//		
if(isset($_POST['doSaveArticle'])){
	// Check if logged in as admin
	if(!$pp->uc->IsAdministrator()){
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));
	}
	else{
		$CArticle->SaveNew(array(null, $_POST['title'], $_POST['author'], $_POST['copyright'], $_POST['description'], $_POST['keywords'], $_POST['article']));
		$pp->ReloadPageAndRemember(array("output"=>"The article was saved.", "output-type"=>"success", "articleId"=>$_POST['id']));
	}
}

$notice = isset($remember['output']) ? "<output class=\" ".$remember['output-type']."\" style=\"display:block\">".$remember['output']."</output><br>" : '';

$page =  <<<EOD
<form method=post action=?p={$p}>
<input type=hidden name=id value="{$article['rowid']}">
<fieldset>
<legend>Edit article</legend>
<p class=right>{$notice}<label for=input4>Description</label><br>
	<textarea class="text span-9 last" name=description id=input4>{$article['description']}</textarea>
</p>
<p>
	<label for=input1>Title</label><br>
	<input type=text class=text name=title id=input1 value="{$article['title']}">
</p>
<p>
	<label for=input2>Author</label><br>
	<input type=text class=text name=author id=input2 value="{$article['author']}">
</p>
<p>
	<label for=input3>Copyright</label><br>
	<input type=text class=text name=copyright id=input3 value="{$article['copyright']}">
</p>
<p>
	<label for=input5>Keywords</label><br>
	<input type=text class=text name=keywords id=input5 value="{$article['keywords']}">
</p>
<hr>
<p>
	<label for=input6>Article</label><br>
	<textarea class="text right last" name=article id=input6 style="width:100%">{$article['article']}</textarea>
</p>
<p>
	<input type=submit name=doSaveArticle {$disabled}>
	<input type=reset>
</p>
</fieldset>
</form>
EOD;
