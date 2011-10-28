<?php

// ------------------------------------------------------------------------------
//
// Check and set the admin password
//
$output = '';
if(isset($_POST['doChangePassword'])) {
	
	// Get and validate the incoming parameters
	$pwd1 = isset($_POST['password1']) ? strip_tags($_POST['password1']) : "";
	$pwd2 = isset($_POST['password2']) ? strip_tags($_POST['password2']) : "";

	// Does passwords match?
	if($pwd1 != $pwd2) {
		$output = "The passwords does not match, re-enter them and make them match.";
	}
		
	// Password can not be empty
	else if(empty($pwd1)) {
		$output = "The password is empty, you can not have an empty password.";	
	}

	// Change the current password
	else {
		CPrinceOfPersia::GetInstance()->SetAdminPassword($pwd1);
		$output = "The password is changed.";
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Change password</h1>
<p>Change the administrator password. The password enables access to the admin area and
enables to change all site configuration.</p>
<form action='?p=changepwd' method=post>
	<fieldset class='std type1'>
		<!-- <legend></legend> -->
		<div class='wrap wrap60'>
			<label>Password:<input type=password name=password1></label>
			<label>Password (again):<input type=password name=password2></label>
			<div class=buttonbar>
				<input type=submit name=doChangePassword value='Change password'>
			</div> 
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

