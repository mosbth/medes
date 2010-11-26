<?php

// ------------------------------------------------------------------------------
//
// Check and set meta information
//
$output = '';
$pp = CPrinceOfPersia::GetInstance();
$meta	= $pp->config['meta'];
		
if(isset($_POST['doSaveMeta'])) {
	
	// Get and validate the incoming parameters
	$meta['author']				= isset($_POST['author']) ? strip_tags($_POST['author']) : "";
	$meta['copyright']		= isset($_POST['copyright']) ? strip_tags($_POST['copyright']) : "";
	$meta['description']	= isset($_POST['description']) ? strip_tags($_POST['description']) : "";
	$meta['keywords']			= isset($_POST['keywords']) ? strip_tags($_POST['keywords']) : "";

	// Perhaps check if the information is reasonable according to seo rules, check size?
	if(false) {
		;
	}
		
	// Save the information
	else {
		$pp->UpdateConfiguration(array('meta'=>$meta));
		$output = "The meta-information is changed.";
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Set meta information</h1>
<p>Set default meta information to enhance search engine visibility. This information is displayed 
as default information on all pages. It can also be modified individually for each page.</p>
<form action='?p={$p}' method=post>
	<fieldset class='std type2'>
		<!-- <legend></legend> -->
		<div class='wrap wrap70'>
			<label>Author:<input type=text name=author value="{$meta['author']}"></label>
			<label>Copyright:<input type=text name=copyright value="{$meta['copyright']}"></label>
			<label>Description:<textarea name=description>{$meta['description']}</textarea></label>
			<label>Keywords:<textarea name=keywords>{$meta['keywords']}</textarea></label>
			<div class='buttonbar'>
				<input type=submit name=doSaveMeta value='Save meta information'>
			</div> 
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

