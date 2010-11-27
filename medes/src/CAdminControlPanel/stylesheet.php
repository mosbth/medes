<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>'', 'styletheme'=>'-1', 'stylelist'=>'-1'));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
$current = $pp->config['styletheme'];

$styletheme = isset($_POST['styletheme']) ? strip_tags($_POST['styletheme']) : $remember['styletheme']; 
$stylelist 	= isset($_POST['stylelist']) ? strip_tags($_POST['stylelist']) : $remember['stylelist']; 

// Read style directory into arrays
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
	//$styleIsWritable 	= is_writable($file) ? "" : "disabled";

	// Get and validate the incoming parameters
	$styleCode = isset($_POST['styleCode']) ? $_POST['styleCode'] : "";

	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error", 'styletheme'=>$styletheme, 'stylelist'=>$stylelist));		
	} 
	
	// Perhaps check if the information is reasonable, validate script?
	else if(!is_writable($file)) {
		$pp->ReloadPageAndRemember(array("output"=>"The file is not writable, could not save file.", "output-type"=>"error", 'styletheme'=>$styletheme, 'stylelist'=>$stylelist));		
	}
		
	// Save the information
	else {
		file_put_contents($file, $styleCode);
		$pp->ReloadPageAndRemember(array("output"=>"The stylesheet was saved to disk.", "output-type"=>"success", 'styletheme'=>$styletheme, 'stylelist'=>$stylelist));		
	}
}


// ------------------------------------------------------------------------------
//
// Use this style theme and stylesheet
//
if(isset($_POST['doSetStyleTheme'])) {

	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error", 'styletheme'=>$styletheme, 'stylelist'=>$stylelist));		
	} 
	
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
		$pp->ReloadPageAndRemember(array("output"=>"This style theme and stylesheet is now the current style.", "output-type"=>"success", 'styletheme'=>$styletheme, 'stylelist'=>$stylelist));		
	} else {
		$pp->ReloadPageAndRemember(array("output"=>"Failed to set the style theme and stylesheet. You must choose both a style theme and a stylesheet.", "output-type"=>"error", 'styletheme'=>$styletheme, 'stylelist'=>$stylelist));		
	}
}


// ------------------------------------------------------------------------------
//
// Get a select/option with all stylesheets
//
$selectFiles  = "<select name=stylelist onChange='submit();'><option value=-1>Choose stylesheet...</option>";
foreach($files as $key => $val) {
  $selectFiles .= "<option value='{$key}'" . ($key == $stylelist ? " selected " : "") . ">{$val}" . ($current['stylesheet'] == $val && $current['name'] == $dirs[$styletheme] ? " [current]" : "") . "</option>";
}
$selectFiles .= "</select>";

$selectDirs  = "<select name=styletheme onChange='form.stylelist.selectedIndex=-1;submit();'><option value=-1>Choose style theme...</option>";
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
	if(!empty($styleIsWritable) && empty($remember['output'])) {
		$remember['output'] .= "This stylesheet is readonly. ";
		$remember['output-type'] .= "info";
	}
	/*
	if(!empty($styleIsDefault)) {
		$remember['output'] .= " This stylesheet is the current style. ";
		$remember['output-type'] = "info";
	} 
	*/
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Select style theme and stylesheet</h1>
<p>Change the look of the site by editing the stylesheets below. Decide which style theme and stylesheet to use.</p>
<form action='?p={$p}' method=post>
	<fieldset>
		<!-- <legend></legend> -->
		<p>
			{$selectDirs}
			{$selectFiles}<br>
			<!-- <label for=input1>label</label><br> -->
			<textarea id=input1 class="wide" name=styleCode>{$styleCode}</textarea>
		</p>
		
		<p class=left>
			<input type=submit name=doSaveStylesheet value='Save stylesheet' {$styleIsWritable} {$disabled}>
			<input type=submit name=doSetStyleTheme value='Use this theme and stylesheet' {$styleIsDefault} {$disabled}>
			<input type=reset value='Reset'>
		</p>

		<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>

	</fieldset>
</form>

EOD;

