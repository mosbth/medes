<?php

// ------------------------------------------------------------------------------
//
// Check and set footer of the site
//
$output = '';
$pp = CPrinceOfPersia::GetInstance();
$footer = htmlentities($pp->config['footer']);
//$footer = htmlentities($pp->config['footer'], ENT_NOQUOTES, 'UTF-8', false);
		
if(isset($_POST['doSaveFooter'])) {
	
	// Get and validate the incoming parameters
	$footer = isset($_POST['footer']) ? $_POST['footer'] : "";

	// Perhaps check if the information is reasonable, validate script?
	if(false) {
		;
	}
		
	// Save the information
	else {
		$pp->UpdateConfiguration(array("footer"=>"$footer"));
		$output = "The footer is changed.";
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Edit the site footer</h1>
<p>Change the look of the site footer by editing the html below.</p>
<form action='?p={$p}' method=post>
	<fieldset class='std type2'>
		<!-- <legend></legend> -->
		<div class='wrap'>
			<label>Footer:<textarea class=code-large name=footer>{$footer}</textarea></label>
			<div class='buttonbar'>
				<input type=submit name=doSaveFooter value='Save footer html'>
			</div> 
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

