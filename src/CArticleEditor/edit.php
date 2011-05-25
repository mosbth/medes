<?php

$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>'', 'newId'=>''));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
$notice = isset($remember['output']) ? "<output class=\" ".$remember['output-type']."\" style=\"display:block\">".$remember['output']."</output><br>" : '';
$save = '';
$delete = '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$id = !empty($remember['newId']) ? $remember['newId'] : $id;

if(!empty($id)){
	$article = $CArticle->GetArticles(array('*'), array(), array('limit'=>1), array('rowid ='=>$id));
	$article = $article['0'];
	$save = "<input type=submit name=doSaveArticle value=\"Save\" {$disabled}>";
    $delete = "<input type=hidden name=delId value={$id}><input type=submit name=delete value=Delete {$disabled}>";
}

$description = isset($article['description']) ? $article['description'] : '';
$title = isset($article['title']) ? $article['title'] : '';
$author = isset($article['author']) ? $article['author'] : '';
$copyright = isset($article['copyright']) ? $article['copyright'] : '';
$keywords = isset($article['keywords']) ? $article['keywords'] : '';
$article = isset($article['article']) ? $article['article'] : ''; // must be last...doh

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
		$CArticle->Save(array('id'=>$_POST['id'], 'title'=>$_POST['title'], 'author'=>$_POST['author'], 'copyright'=>$_POST['copyright'], 'description'=>$_POST['description'], 'keywords'=>$_POST['keywords'], 'article'=>$_POST['article']));
		$pp->ReloadPageAndRemember(array("output"=>"The article was saved.", "output-type"=>"success"));
	}
}

if(isset($_POST['doSaveNewArticle'])){
	// Check if logged in as admin
	if(!$pp->uc->IsAdministrator()){
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));
	}
	else{
		$newId = $CArticle->SaveNew(array('title'=>$_POST['title'], 'author'=>$_POST['author'], 'copyright'=>$_POST['copyright'], 'description'=>$_POST['description'], 'keywords'=>$_POST['keywords'], 'article'=>$_POST['article'], 'owner'=>'article'));
		$pp->ReloadPageAndRemember(array("output"=>"The article was saved.", "output-type"=>"success", "newId"=>$newId));
		//header('location: ?p=home;');
	}
}

if(isset($_POST['delete'])){
  if(!$pp->uc->IsAdministrator()){
      $pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));
  }
  else{
      $CArticle->Delete($_POST['delId']);
      $pp->ReloadPageAndRemember(array("output"=>"The article was deleted.", "output-type"=>"success"));
  }
}

$page =  <<<EOD
<form method=post action=?p={$p}&id={$id}>
<input type=hidden name=id value="{$id}">
<fieldset>
<legend>Edit article</legend>
<div class=span-8>
<p>
	<label for=input1>Title</label><br>
	<input type=text class=text name=title id=input1 value="{$title}">
</p>
<p>
	<label for=input2>Author</label><br>
	<input type=text class=text name=author id=input2 value="{$author}">
</p>
<p>
	<label for=input3>Copyright</label><br>
	<input type=text class=text name=copyright id=input3 value="{$copyright}">
</p>
<p>
	<label for=input5>Keywords</label><br>
	<input type=text class=text name=keywords id=input5 value="{$keywords}">
</p>
</div>
<div class="span-9 last">
<p>
{$notice}
    <label for=input4>Description</label><br>
	<textarea class="text" style="width:100%" name=description id=input4>{$description}</textarea>
</p>
</div>
<p>
	<label for=input6>Article</label><br>
	<textarea class="text last" name=article id=input6 style="width:100%">{$article}</textarea>
</p>
<p>
	{$save}
	<input type=submit name=doSaveNewArticle value="Save As New" {$disabled}>
    {$delete}
	<input type=reset>
</p>
</fieldset>
</form>
EOD;

