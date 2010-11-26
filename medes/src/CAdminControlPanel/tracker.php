<?php

// ------------------------------------------------------------------------------
//
// Check and set meta information
//
$output = '';
$pp = CPrinceOfPersia::GetInstance();
$tracker = $pp->config['tracker'];
//$tracker = $pp->googleAnalytics;
		
if(isset($_POST['doSaveTracker'])) {
	
	// Get and validate the incoming parameters
	$tracker = isset($_POST['tracker']) ? strip_tags($_POST['tracker'], '<script>') : "";

	// Perhaps check if the information is reasonable, validate script?
	if(false) {
		;
	}
		
	// Save the information
	else {
		$pp->UpdateConfiguration(array("tracker"=>"$tracker"));
		$output = "The tracker-information is changed.";
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
	<fieldset class='std type2'>
		<!-- <legend></legend> -->
		<div class='wrap'>
			<label>Tracker:<textarea class=code name=tracker>{$tracker}</textarea></label>
			<div class='buttonbar'>
				<input type=submit name=doSaveTracker value='Save tracker code'>
			</div> 
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

