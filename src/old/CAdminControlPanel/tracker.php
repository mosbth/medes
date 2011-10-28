<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
$tracker = $pp->config['tracker'];


// ------------------------------------------------------------------------------
//
// Check and set the sitelink
//
if(isset($_POST['doSaveTracker'])) {
	
	// Get, sanitize and validate incomings
	$inputs = filter_input_array(INPUT_POST, array(
		'tracker' => array('filter'	=> FILTER_UNSAFE_RAW),
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
		$pp->UpdateConfiguration(array("tracker"=>$inputs['tracker']));
		$pp->ReloadPageAndRemember(array("output"=>"The tracker-information is changed.", "output-type"=>"success"));
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Set tracker</h1>
<p>Use Google Analytics (GA) to track visits to site. Copy the javascript from GA and save it here.</p>
<form action='?p={$p}' method=post>
	<fieldset>
		<!-- <legend></legend> -->
		<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>
		
		<p>
			<label for=input1>Tracker code:</label><br>
			<textarea id=input1 class="text" name=tracker>{$tracker}</textarea>
		</p>
		
		<p>
			<input type=submit name=doSaveTracker value='Save tracker code' {$disabled}>
			<input type=reset value='Reset'>
		</p>
		
	</fieldset>
</form>

EOD;
