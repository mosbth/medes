<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
$header = $pp->config['header'];


// ------------------------------------------------------------------------------
//
// Check and set the sitelink
//
if(isset($_POST['doSaveHeader'])) {
	
	// Get, sanitize and validate incomings
	$inputs = filter_input_array(INPUT_POST, array(
		'header' => array('filter'	=> FILTER_UNSAFE_RAW),
		)
	);

	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 
	
	// Perhaps check if the siteurl is a valid phpmedes siteurl?
	else if(false) {
		;
	}
		
	// Set the siteurl
	else {
		$pp->UpdateConfiguration(array("header"=>$inputs['header']));
		$pp->ReloadPageAndRemember(array("output"=>"The header is changed.", "output-type"=>"success"));
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Edit the site header and logo</h1>
<p>Change the look of the site header by editing the html below.</p>
<form action='?p={$p}' method=post>
	<fieldset>
		<!-- <legend></legend> -->		
		<p>
			<label for=input1>HTML for page-header:</label><br>
			<textarea id=input1 class="wide" name=header>{$header}</textarea>
		</p>
		
		<p class=left>
			<input type=submit name=doSaveHeader value='Save header html' {$disabled}>
			<input type=reset value='Reset'>
		</p>

		<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>

	</fieldset>
</form>

EOD;

