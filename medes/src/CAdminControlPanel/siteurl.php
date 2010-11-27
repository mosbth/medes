<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
$siteurl = $pp->siteUrl;


// ------------------------------------------------------------------------------
//
// Check and set the sitelink
//
if(isset($_POST['doSetSiteUrl'])) {
	
	// Get, sanitize and validate incomings
	$inputs = filter_input_array(INPUT_POST, array(
		'siteurl' => array('filter'	=> FILTER_SANITIZE_URL),
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
		$pp->UpdateConfiguration(array("siteurl"=>$inputs['siteurl']));
		$pp->ReloadPageAndRemember(array("output"=>"The sitelink is changed.", "output-type"=>"success"));
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Set the site link</h1>
<p>Set the main link to the site. The link should point to the directory, not to a page.
The link ends with a slash. You may leave out the protocol and server. 
</p>
<p>
The sitelink is set
automatically during the installation procedure.
</p>
<form action='?p={$p}' method=post>
	<fieldset>
		<!-- <legend></legend> -->
		<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>

		<p>
			<label for=input1>Site link:</label><br>
			<input id=input1 class="text" type=text name=siteurl value={$siteurl}>
		</p>
		
		<p>
			<input type=submit name=doSetSiteUrl value='Save meta information' {$disabled}>
			<input type=reset value='Reset'>
		</p>
		
	</fieldset>
</form>

EOD;

