<div class=span-18>
	{$statusBar}
	<form action='?p={$this->key}&amp;e' method=post>
		<fieldset>
			<legend>Edit page: <a href='?p={$this->key}&amp;a=renamePage'>{$this->key}</a></legend>		
			<p>
				<label for=input1>Content:</label><br>
				<textarea id=input1 class="wide" name=content>{$content}</textarea>
			</p>
			<p class=left>
				<input type=submit name=doPublishPage value='Publish' {$disabled}>
				<input type=submit name=doSaveDraftPage value='Save' {$disabled}>
				<input type=reset value='Reset'>
				{$draft}
			</p>
			<p class=right><output class="span-5 {$remember['output-type']}">{$remember['output']}</output></p>	
		</fieldset>
	</form>
</div>
<div class="span-6 last quiet">
	{$details}
</div>

<span class=quiet>Draft exists, <a href='?p={$this->key}&amp;draft'>preview it</a>.</span>
