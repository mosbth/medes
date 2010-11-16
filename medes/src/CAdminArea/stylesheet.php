<?php

// ------------------------------------------------------------------------------
//
// Initial settings
//
$output = '';
$pp = CPrinceOfPersia::GetInstance();
$styleDir = $pp->medesPath . "/style/";
$stylesheet = $pp->config['stylesheet'];
$stylelist = isset($_POST['stylelist']) ? strip_tags($_POST['stylelist']) : -1; 
$files = $pp->ReadDirectory($styleDir);


// ------------------------------------------------------------------------------
//
// Check and set stylesheet of the site
//		
if(isset($_POST['doSaveStylesheet'])) {
	
	$file 						= "{$styleDir}/{$files[$stylelist]}";
	$styleCode 				= htmlentities(file_get_contents($file));
	$styleIsWritable 	= is_writable($file) ? "" : "disabled";

	// Get and validate the incoming parameters
	$styleCode = isset($_POST['styleCode']) ? $_POST['styleCode'] : "";

	// Perhaps check if the information is reasonable, validate script?
	if(!is_writable($file)) {
		$output .= "The file is not writable, could not save file.";
	}
		
	// Save the information
	else {
		file_put_contents($file, $styleCode);
		$output .= "The stylesheet was saved to disk.";
	}
}


// ------------------------------------------------------------------------------
//
// Use this stylesheet
//
if(isset($_POST['doSetStylesheet'])) {
	
	$pp->UpdateConfiguration(array("stylesheet"=>$files[$stylelist]));
	$stylesheet = $pp->config['stylesheet'];
	$output .= "This stylesheet is now the current style.";
}


// ------------------------------------------------------------------------------
//
// Get a select/option with all stylesheets
//
$select  = "<select name=stylelist onChange='submit();'><option value=-1>Choose stylesheet</option>";
foreach($files as $key => $val) {
  $select .= "<option value='{$key}'" . ($key == $stylelist ? " selected " : "") . ">{$val}" . ($stylesheet == $val ? " [current]" : "") . "</option>";
}
$select .= "</select>";


// ------------------------------------------------------------------------------
//
// Get stylesheet content and check if file is writable or if its choosen as default
//
$styleCode = "";
$styleIsWritable = "disabled";
$styleIsDefault = "disabled";
if($stylelist >= 0 && $stylelist < count($files)) {
	$file 						= "{$styleDir}/{$files[$stylelist]}";
	$styleCode 				= htmlentities(file_get_contents($file));
	$styleIsWritable 	= is_writable($file) ? "" : "disabled";
	$styleIsDefault 	= $stylesheet == $files[$stylelist] ? "disabled" : "";
	if(!empty($styleIsWritable)) {
		$output .= "This stylesheet is readonly.";
	}
	if(!empty($styleIsDefault)) {
		$output .= "This stylesheet is the current style.";
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Edit the stylesheets</h1>
<p>Change the look of the site by editing the stylesheets below. Decide which stylesheet to use.</p>
<form action='?p={$p}' method=post>
	<fieldset class='std type2'>
		<!-- <legend></legend> -->
		<legend>{$select}</legend>
		<div class=wrap>
			<!-- {$select} -->
			<textarea class=code-xlarge name=styleCode>{$styleCode}</textarea>
			<div class=buttonbar>
				<input type=submit name=doSaveStylesheet value='Save stylesheet' {$styleIsWritable}>
				<input type=submit name=doSetStylesheet value='Use this stylesheet' {$styleIsDefault}>
			</div> 
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

