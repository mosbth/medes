<?php
// ============================================================================================
//
// License:
// This script is licensed under the Creative Commons Attribution Share-Alike License v3.0  
// or any later version. (http://creativecommons.org/licenses/by-sa/3.0/)
//
// Description: 
// This script enables changing stylesheet of a website through a select/option. The script 
// stores the choosen stylesheet in the session.
//
// Usage:
// Include using include() at top of the header.php-page (or equal). Use the following 
// code to create the reference to the stylesheet and to print the form.
//
// $stylePath
// $styleTitle
// $formChooseStyle
//
// Ensure that you have started the session using session_start(). Place it at the top of the 
// page, for example in header.php. Use named sessions to avoid problems when running on a 
// shared host.
// http://php.net/manual/en/function.session-start.php
// http://php.net/manual/en/function.session-name.php
//
// Author:
// Copyright 2010 Mikael Roos (mos@bth.se)
//
// History:
// 2010-08-25: First try.
//

// --------------------------------------------------------------------------------------------
//
// Define an array with arrays of the available stylesheets
//
// http://php.net/manual/en/language.types.array.php
//
$styles = Array(
		"default" 	=> Array("title" => "phpmedes default", 	"path" => "style/stylesheet_default.css"),	
	);


// --------------------------------------------------------------------------------------------
//
// Is form submitted? Check if style is set in $_GET. Then change the current choice of style
//
// http://php.net/manual/en/reserved.variables.get.php
// http://php.net/manual/en/function.isset.php
//
if(isset($_GET['choose-style'])) {

	// Check if the choosen style is valid, does it exists?
	if(isset($styles[$_GET['choose-style']])) {
	
		// Set the current choice of stylesheet in the session
		// http://se.php.net/manual/en/reserved.variables.session.php
		$_SESSION['style'] 	= $_GET['choose-style'];
	}
}


// --------------------------------------------------------------------------------------------
//
// Get current selected style from $_SESSION, if any. Start with default values. 
// Change it if the session is set.
//
// Read on the difference between single-quoted and double-quoted strings
// http://php.net/manual/en/language.types.string.php
//
$styleName	= "default";
$styleTitle	= $styles[$styleName]['title'];
$stylePath	= $styles[$styleName]['path'];

if(isset($_SESSION['style'])) {

	// There is a session with a choosen style, use it
	$styleName	= $_SESSION['style'];
	$styleTitle	= $styles[$styleName]['title'];
	$stylePath	= $styles[$styleName]['path'];
}


// --------------------------------------------------------------------------------------------
//
// Create the form to choose stylesheet
//
// http://php.net/manual/en/control-structures.foreach.php
// http://php.net/manual/en/language.operators.string.php
// http://www.php.net/manual/en/language.types.string.php#language.types.string.parsing
// http://php.net/manual/en/language.operators.comparison.php#language.operators.comparison.ternary
// http://se.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc
// 
$formOptions="";
foreach($styles as $key => $val) {
	// Create each option, set selected on the current style/option
	$formOptions .= "<option value='$key'" . ($key == $styleName ? ' selected ' : '') . ">{$val['title']}</option>";
}

$formChooseStyle = <<<EOD
<!-- Form to choose style -->
<form class="choose-style" action="?" method="get">
	<label>Stylesheet:
		<select name="choose-style" onchange="submit();">
			$formOptions
		</select>
	</label>
</form>
EOD;


// --------------------------------------------------------------------------------------------
//
// echo and print_r to get some help with debugging
//
// http://php.net/manual/en/function.print-r.php
// http://php.net/manual/en/function.echo.php
//
if(isset($_GET['debug'])) {
	echo "<div class='debug'>";
	echo "<h2>Debug</h2>";
	echo "<p>Style name: $styleName<br>";
	echo "Style title: $styleTitle<br>";
	echo "Style path: $stylePath<br>";
	echo "<h3>_GET</h3><pre>";
	print_r($_GET);
	echo "</pre>";
	echo "<h3>_SESSION</h3>";
	echo "<p>Session name is: $sessionName";
	echo "<p><a href='destroy_session.php?name=$sessionName'>Destroy session</a>";
	echo "<pre>";
	print_r($_SESSION);
	echo "</pre>";
	echo "<h3>Change Style</h3>";	
	echo $formChooseStyle;
	echo "</div>";
}



?>