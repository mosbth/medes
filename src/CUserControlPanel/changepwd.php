<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";


// ------------------------------------------------------------------------------
//
// Change password
//
if(isset($_POST['doChangePassword'])) {
	
	// Get and validate the incoming parameters
	$inputs = filter_input_array(INPUT_POST, array(
		'password0' => array('filter'	=> FILTER_SANITIZE_STRING),
		'password1' => array('filter'	=> FILTER_SANITIZE_STRING),
		'password2' => array('filter'	=> FILTER_SANITIZE_STRING),
		)
	);

	// Is current password ok?
	if(!$pp->CheckAdminPassword($inputs['password0'])) {
		$pp->ReloadPageAndRemember(array("output"=>"The current password did not match.", "output-type"=>"error"));
	}
	
	// Does passwords match?
	else if($inputs['password1'] != $inputs['password2']) {
		$pp->ReloadPageAndRemember(array("output"=>"The passwords does not match, re-enter them and make them match.", "output-type"=>"error"));		
	}
		
	// Password can not be empty
	else if(empty($inputs['password1'])) {
		$pp->ReloadPageAndRemember(array("output"=>"The password is empty, you can not have an empty password.", "output-type"=>"error"));		
	}

	// Change the current password
	else {
		$pp->SetAdminPassword($inputs['password1']);
		$pp->ReloadPageAndRemember(array("output"=>"The password is changed.", "output-type"=>"success"));
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Change password</h1>
<p>Change your password. </p>
<form action='?p=changepwd' method=post>
	<fieldset class='std type1'>
		<!-- <legend></legend> -->
		<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>

		<p>
			<label>Current password:</label><br>
			<input type=password class=text name=password0>
		</p>
		
		<p>
			<label>New password:</label><br>
			<input type=password class=text name=password1>
		</p>
		
		<p>
			<label>New password (again):</label><br>
			<input type=password class=text name=password2>
		</p>
		
		<p>
			<input type=submit name=doChangePassword value='Change password' {$disabled}>
		</p>
		
	</fieldset>
</form>
EOD;

