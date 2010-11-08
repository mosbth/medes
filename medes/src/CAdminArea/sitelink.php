<?php

// ------------------------------------------------------------------------------
//
// Check and set the sitelink
//
$output = '';
$siteurl = CPrinceOfPersia::GetInstance()->siteUrl;
if(isset($_POST['doSetSiteUrl'])) {
	
	// Get and validate the incoming parameters
	$siteurl = isset($_POST['siteurl']) ? strip_tags($_POST['siteurl']) : "";

	// Perhaps check if the siteurl is a valid phpmedes siteurl?
	if(false) {
		;
	}
		
	// Set the siteurl
	else {
		CPrinceOfPersia::GetInstance()->SetSiteUrl($siteurl);
		$output = "The sitelink is changed.";
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Set the site link</h1>
<p>Set the main link to the site. The link should point to the directory, not to a page.
The link ends with a slash. You may leave out the protocol and server. Leave the field empty if the link is 
the root of the server.</p>
<form action='?p={$p}' method=post>
	<fieldset class='std type2'>
		<!-- <legend></legend> -->
		<div class='wrap wrap60'>
			<label>Site link:<input type=text name=siteurl value="{$siteurl}"></label>
			<div class='buttonbar'>
				<input type=submit name=doSetSiteUrl value='Set site link'>
			</div> 
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

