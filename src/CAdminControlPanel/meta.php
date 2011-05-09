<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
$meta	= $pp->config['meta'];


// ------------------------------------------------------------------------------
//
// Check and set meta information
//	
if(isset($_POST['doSaveMeta'])) {
	
	// Get, sanitize and validate incomings
	$inputs = filter_input_array(INPUT_POST, array(
		'author' => array('filter'	=> FILTER_SANITIZE_STRING),
		'copyright' => array('filter'	=> FILTER_SANITIZE_STRING),
		'description' => array('filter'	=> FILTER_SANITIZE_STRING),
		'keywords' => array('filter'	=> FILTER_SANITIZE_STRING),
		)
	);

	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 
	
	// Perhaps check if the information is reasonable according to seo rules, check size?
	else if(false) {
		;
	}
		
	// Save the information
	else {
		$pp->UpdateConfiguration(array('meta'=>$inputs));
		$pp->ReloadPageAndRemember(array("output"=>"The meta-information is changed.", "output-type"=>"success"));
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Set meta information</h1>
<p>Set default meta information to enhance search engine visibility. This information is displayed 
as default information on all pages. It can also be modified individually for each page.</p>
<form action='?p={$p}' method=post>
	<fieldset class='std type2'>
		<!-- <legend></legend> -->
		<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>

		<p>
			<label for=input1>Author:</label><br>
			<input id=input1 class="text" type=text name=author value="{$meta['author']}">
		</p>
			
		<p>
			<label for=input2>Copyright:</label><br>
			<input id=input2 class="text" type=text name=copyright value="{$meta['copyright']}">
		</p>
		
		<p>
			<label for=input3>Description:</label><br>
			<textarea id=input3 class="text" name=description>{$meta['description']}</textarea>
		</p>
		
		<p>
			<label for=input4>Keywords:</label><br>
			<textarea id=input4 class="text" name=keywords>{$meta['keywords']}</textarea>
		</p>
		
		<p>
			<input type=submit name=doSaveMeta value='Save meta information' {$disabled}>
			<input type=reset value='Reset'>
		</p>

	</fieldset>
</form>

EOD;

