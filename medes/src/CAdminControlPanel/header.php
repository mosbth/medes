<?php

// ------------------------------------------------------------------------------
//
// Check and set header of the site
//
$output = '';
$pp = CPrinceOfPersia::GetInstance();
$header = $pp->config['header'];
		
if(isset($_POST['doSaveHeader'])) {
	
	// Get and validate the incoming parameters
	$header = isset($_POST['header']) ? $_POST['header'] : "";

	// Perhaps check if the information is reasonable, validate script?
	if(false) {
		;
	}
		
	// Save the information
	else {
		$pp->UpdateConfiguration(array("header"=>"$header"));
		$output = "The header is changed.";
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
	<fieldset class='std type2'>
		<!-- <legend></legend> -->
		<div class='wrap'>
			<label>Header:<textarea class=code-large name=header>{$header}</textarea></label>
			<div class='buttonbar'>
				<input type=submit name=doSaveHeader value='Save header html'>
			</div> 
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

