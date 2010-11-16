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


// ------------------------------------------------------------------------------
//
// Get a select/option with all stylesheets
//
$files = $pp->ReadDirectory($styleDir);

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
// Check and set stylesheet of the site
//		
if(isset($_POST['doSaveStylesheet'])) {
	
	// Get and validate the incoming parameters
	$stylesheet = isset($_POST['stylesheet']) ? $_POST['stylesheet'] : "";

	// Perhaps check if the information is reasonable, validate script?
	if(false) {
		;
	}
		
	// Save the information
	else {
		$pp->UpdateConfiguration(array("stylesheet"=>"$stylesheet"));
		$output .= "The stylesheet is changed.";
	}
}


// ------------------------------------------------------------------------------
//
// Use this stylesheet
//
if(isset($_POST['doSetStylesheet'])) {
	
	$pp->UpdateConfiguration(array("stylesheet"=>$files[$stylelist]));
	$output .= "This stylesheet is now the current style.";
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

