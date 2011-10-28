<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>'', 'account'=>''));

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
		$uc = CUserController::GetInstance();
		$uc->Populate($inputs['account'], 1);
		$uc->StoreInSession();		
		$pp->ReloadPageAndRemember(array("output"=>"You are now logged in as administrator.", "output-type"=>"success", "account"=>$inputs['account']));
	}	else {
		$pp->ReloadPageAndRemember(array("output"=>"You failed to login. The account does not exists or the password does not match the account.", "output-type"=>"notice"));
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Login</h1>
<p>Login using your userid or email together with the password.</p>
<form action='?p={$p}' method=post>
	<fieldset>
		<!-- <legend></legend> -->
		<p class="right"><output class="span-7 {$remember['output-type']}">{$remember['output']}</output></p>

		<p>
			<label for=account>Account or email:</label><br>
			<input id=account class="text" type=text name=account value={$remember['account']}>		
		</p>
		
		<p>
			<label for=password>Password:</label><br>
			<input id=password class="text" type=password name=password>
		</p>
		
		<p>
			<input type=submit name=doLogin value='Login'>
		</p>
	</fieldset>
</form>
EOD;

