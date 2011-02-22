<?php

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>'', 'htmlpart'=>'-1'));
$htmlpart = filter_input(INPUT_POST, 'htmlpart', FILTER_UNSAFE_RAW);
$htmlpart = $htmlpart == null ? $remember['htmlpart'] : $htmlpart; // Set default value if not choosen
$disabled = $pp->uc->IsAdministrator() && $htmlpart != -1 ? "" : "disabled";

// Create a select/option for the parts to be edited
$items = array(
	"-1"=>array("key"=>null, "text"=>"Choose htmlpart of site to edit...", "html"=>""),
	"htmlhead"=>array("key"=>"htmlparts-htmlhead", "text"=>"head-section of html", "html"=>$pp->config['htmlparts-htmlhead']),
	"pagetop"=>array("key"=>"htmlparts-pagetop", "text"=>"html for page-top", "html"=>$pp->config['htmlparts-pagetop']),
	"pageheader"=>array("key"=>"htmlparts-pageheader", "text"=>"html for page-header", "html"=>$pp->config['htmlparts-pageheader']),
	"pagecontent"=>array("key"=>"htmlparts-pagecontent", "text"=>"html for page-content", "html"=>$pp->config['htmlparts-pagecontent']),
	"pagefooter"=>array("key"=>"htmlparts-pagefooter", "text"=>"html for page-footer", "html"=>$pp->config['htmlparts-pagefooter']),
	"pagebottom"=>array("key"=>"htmlparts-pagebottom", "text"=>"html for page-bottom", "html"=>$pp->config['htmlparts-pagebottom']),
);

$selectHtml  = "<select name=htmlpart onChange='submit();'>";
foreach($items as $key=>$val) {
  $selectHtml .= "<option value='{$key}'" . ($key == $htmlpart ? " selected " : "") . ">{$val['text']}</option>";
}
$selectHtml .= "</select>";


// ------------------------------------------------------------------------------
//
// Check and set the sitelink
//
if(isset($_POST['doSaveHtml'])) {
	
	// Get, sanitize and validate incomings
	$inputs = filter_input_array(INPUT_POST, array(
		'code' => array('filter'	=> FILTER_UNSAFE_RAW),
		)
	);

	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 
	
	// Perhaps check if the htmlpart is valid?
/*
	ob_start();
	$ret = eval("?>" . $inputs['code']);
	ob_get_clean();
	echo var_dump(php_check_syntax($inputs['code']));
	
//if('' !== $error = ob_get_clean()) {
	if($ret === false) {
		$pp->ReloadPageAndRemember(array("output"=>"Syntax error in PHP, review your code.", "output-type"=>"error", "htmlpart"=>$htmlpart));		
	}
*/
		
	// Set the htmlpart
	else {
		$pp->UpdateConfiguration(array($items[$htmlpart]['key']=>$inputs['code']));
		$pp->ReloadPageAndRemember(array("output"=>"The {$items[$htmlpart]['text']} is changed.", "output-type"=>"success", "htmlpart"=>$htmlpart));
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Edit parts of html for the site</h1>
<p>Change the look of the site by editing parts of it html.</p>
<form action='?p={$p}' method=post>
	<fieldset>
		<!-- <legend></legend> -->		
		<p>
			{$selectHtml}<br>
			<!--<label for=input1>{$items[$htmlpart]['text']}</label><br> -->
			<textarea id=input1 class="wide" name=code>{$items[$htmlpart]['html']}</textarea>
		</p>
		
		<p class=left>
			<input type=submit name=doSaveHtml value='Save html' {$disabled}>
			<input type=reset value='Reset'>
		</p>

		<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>

	</fieldset>
</form>

EOD;

