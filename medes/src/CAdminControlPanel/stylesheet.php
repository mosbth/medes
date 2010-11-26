<?php

// ------------------------------------------------------------------------------
//
// Initial settings
//
$output = '';
$pp = CPrinceOfPersia::GetInstance();
$current = $pp->config['styletheme'];

$styletheme = isset($_POST['styletheme']) ? strip_tags($_POST['styletheme']) : -1; 
$stylelist 	= isset($_POST['stylelist']) ? strip_tags($_POST['stylelist']) : -1; 

$styleDir = $pp->medesPath . "/style/";
$dirs = $pp->ReadDirectory($styleDir, array('dir'));
$files = array();
if($styletheme >= 0 && $styletheme < count($dirs)) {
	$styleDir .= basename($dirs[$styletheme]);
	$files 	= $pp->ReadDirectory($styleDir, array('file'));
}


// ------------------------------------------------------------------------------
//
// Save stylesheet code
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
// Use this style theme and stylesheet
//
if(isset($_POST['doSetStyleTheme'])) {

	if($stylelist >= 0 && $stylelist < count($files) &&
		 $styletheme >= 0 && $styletheme < count($dirs)) {
		 
		$config['styletheme'] = array(
			"name"=>$dirs[$styletheme],
			"stylesheet"=>$files[$stylelist],
		);
		$config['styletheme']['print'] 	= is_file("{$styleDir}/print.css") ? "print.css" : null;
		$config['styletheme']['ie'] 		= is_file("{$styleDir}/ie.css") ? "ie.css" : null;

		$pp->UpdateConfiguration(array("styletheme"=>$config['styletheme']));
		$current = $pp->config['styletheme'];
		$output .= "This style theme and stylesheet is now the current style.";
	} else {
		$output .= "Failed to set the style theme and stylesheet. You must choose both a style theme and a stylesheet.";
	}
}


// ------------------------------------------------------------------------------
//
// Get a select/option with all stylesheets
//
$selectFiles  = "<select name=stylelist onChange='submit();'><option value=-1>Choose stylesheet</option>";
foreach($files as $key => $val) {
  $selectFiles .= "<option value='{$key}'" . ($key == $stylelist ? " selected " : "") . ">{$val}" . ($current['stylesheet'] == $val && $current['name'] == $dirs[$styletheme] ? " [current]" : "") . "</option>";
}
$selectFiles .= "</select>";

$selectDirs  = "<select name=styletheme onChange='form.stylelist.selectedIndex=-1;submit();'><option value=-1>Choose style theme</option>";
foreach($dirs as $key => $val) {
  $selectDirs .= "<option value='{$key}'" . ($key == $styletheme ? " selected " : "") . ">{$val}" . ($current['name'] == $val ? " [current]" : "") . "</option>";
}
$selectDirs .= "</select>";


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
	$styleIsDefault 	= $current['stylesheet'] == $files[$stylelist] && $current['name'] == $dirs[$styletheme] ? "disabled" : "";
	if(!empty($styleIsWritable)) {
		$output .= "This stylesheet is readonly. ";
	}
/*	if(!empty($styleIsDefault)) {
		$output .= "This stylesheet is the current style. ";
	} */
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Select style theme and stylesheet</h1>
<p>Change the look of the site by editing the stylesheets below. Decide which style theme and stylesheet to use.</p>
<form action='?p={$p}' method=post>
	<fieldset class='std type2'>
		<!-- <legend></legend> -->
		<legend>{$selectDirs}{$selectFiles}</legend>
		<div class=wrap>
			<textarea class=code-xlarge name=styleCode>{$styleCode}</textarea>
			<div class=buttonbar>
				<input type=submit name=doSaveStylesheet value='Save stylesheet' {$styleIsWritable}>
				<input type=submit name=doSetStyleTheme value='Use this theme and stylesheet' {$styleIsDefault}>
			</div> 
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

