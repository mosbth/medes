<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'','account'=>''));

// Get, sanitize and validate incomings
$inputs = filter_input_array(INPUT_POST, array(
	'account' => array('filter'	=> FILTER_SANITIZE_STRING),
	'password' => array('filter'	=> FILTER_UNSAFE_RAW),		
	)
);


// ------------------------------------------------------------------------------
//
// Try to login the user
//
if(isset($_POST['doLogin'])) {

	// Do whats to be done
	if(false) {
		Throw new Exception("Check if something is very wrong?");
	} else if(in_array($inputs['account'], array('adm', 'admin', 'root')) && $pp->CheckAdminPassword($inputs['password'])) {
		$pp->ReloadPageAndRemember(array("output"=>"You are now logged in as administrator.", "account"=>$inputs['account']));
	}	else {
		$pp->ReloadPageAndRemember(array("output"=>"You failed to login. The account does not exists or the password does not match the account."));
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Login</h1>
<p>Login using your userid or email together with the password.</p>
<form class=inline action='?p={$p}' method=post>
	<fieldset>
		<!-- <legend></legend> -->
		<label for=account class="span-4">Account or email:</label>
		<input id=account class="text span-8" type=text name=account value={$remember['account']}>		
		
		<label for=password class="clear span-4">Password:</label>
		<input id=password class="text span-8" type=password name=password>

		<div class='buttonbar clear prepend-4 span-8'>
			<input class="right span-2 large" type=submit name=doLogin value='Login'>
		</div> 
		<output class="clear span-16 last">{$remember['output']}</output> 
	</fieldset>
</form>
EOD;

