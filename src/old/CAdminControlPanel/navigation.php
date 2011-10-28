<?php

//
// Notes.
// $output is not used? neither $remember.
//

// ------------------------------------------------------------------------------
//
// Do general settings
//
$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>'', 'outputItem'=>'', 'outputItem-type'=>''));
$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";

$outputItem = '';

// What is the current choice?
$selectedLevel1 = isset($_POST['selectLevel1']) ? $_POST['selectLevel1'] : "none-choosen";

// Create an array of all navigational menus
$configItem = 'navigation';
$items = $pp->config[$configItem];

$options = array("none-choosen"=>array("text"=>"Choose navigational menu...", "nav"=>array()));
$options = array_merge($options, $items);

// current choice
$navbar = $options[$selectedLevel1]['nav'];

$selectLevel1  = "<select name=selectLevel1 class=span-5 onChange='form.navbarlist.selectedIndex=-1;submit();'>";
foreach($options as $key=>$val) {
  $selectLevel1 .= "<option value='{$key}'" . ($key == $selectedLevel1 ? " selected " : "") . ">{$val['text']}</option>";
}
$selectLevel1 .= "</select>";


// ------------------------------------------------------------------------------
//
// Add a custom menu
//
if(isset($_GET['addMenu'])) {
	
	// Get and validate the incoming parameters
	$inputs = filter_input_array(INPUT_GET, array(
		'key' => array('filter'	=> FILTER_UNSAFE_RAW),
		'text' => array('filter'	=> FILTER_UNSAFE_RAW),
		)
	);

	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 

	// Check that all values are there
	else if(!(isset($inputs['key']) && isset($inputs['text']))) {
		Throw new Exception("addMenu: You must set both 'key' and 'text' for the menu.");		
	}	

	// Save the information
	else {
		$items[$inputs['key']] = array(
			"text"=>$inputs['key'],
			"nav"=>array()
		);
		$pp->UpdateConfiguration(array($configItem=>$items));
		$outputItem = "The menu was added.";
	}
}


// ------------------------------------------------------------------------------
//
// Delete a custom menu
//
if(isset($_GET['delMenu'])) {
	
	// Get and validate the incoming parameters
	$inputs = filter_input_array(INPUT_GET, array(
		'key' => array('filter'	=> FILTER_UNSAFE_RAW),
		)
	);

	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 

	// Check that all values are there
	else if(!(isset($inputs['key']))) {
		Throw new Exception("addMenu: You must set 'key'.");		
	}	

	// Save the information
	else {
		unset($items[$inputs['key']]);
		$pp->UpdateConfiguration(array($configItem=>$items));
		$outputItem = "The menu was deleted.";
	}
}


// ------------------------------------------------------------------------------
//
// Save details on an item
//
if(isset($_POST['doSaveItem'])) {
	
	// Get and validate the incoming parameters
	$current 				= isset($_POST['navbarlist']) ? strip_tags($_POST['navbarlist']) : 0;
	$item['text'] 	= isset($_POST['text']) ? strip_tags($_POST['text']) : "";
	$item['url'] 		= isset($_POST['url']) ? strip_tags($_POST['url']) : "";
	$item['title'] 	= isset($_POST['title']) ? strip_tags($_POST['title']) : "";

	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 
	
	// Needs to have an item selected to be able to save it
	else if(!$current) {
		Throw new Exception("Item is not selected and con not be saved. This should not happen. Report this as an error.");		
	}	

	// Save the information
	else {
		$navbar[$current] = $item;
		$items[$selectedLevel1]['nav'] = $navbar;
		$pp->UpdateConfiguration(array($configItem=>$items));
		$outputItem = "The information was saved.";
	}
}


// ------------------------------------------------------------------------------
//
// Add new item
//
else if(isset($_POST['doAddItem'])) {
	
	$noItems = count($navbar);
	
	// Get, sanitize and validate incomings
	$args = array(
		'navbarlist' => array('filter'	=> FILTER_VALIDATE_INT, 
													'options'	=> array('default'=>$noItems, 'min_range'=>1, 'max_range'=>$noItems)
													),
	);
	$inputs = filter_input_array(INPUT_POST, $args);
	//$current = $inputs['navbarlist']; // http://bugs.php.net/50632 fails to set default value when POST is not set
	$current = $inputs['navbarlist'] === null ? $noItems : $inputs['navbarlist'];
	
	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 
	
	// Do whats to be done
	else if($current === false) {
		Throw new Exception("doAddItem, selected item ($current) is out of range.");
	} else {
		$newItem['text'] 	= "new item";
		$newItem['url'] 	= "medes/page/template.php";
		$newItem['title'] = "new item title";

		// Move all items +1 from $current
		for($i=$noItems;$i>$current;$i--) {
			$navbar[$i+1] = $navbar[$i];
		}
	
		// Add item and save
		$navbar[$current+1] = $newItem;
		$_POST['navbarlist'] = $current+1;
		$items[$selectedLevel1]['nav'] = $navbar;
		$pp->UpdateConfiguration(array($configItem=>$items));
	}
}


// ------------------------------------------------------------------------------
//
// Delete item
//
else if(isset($_POST['doDelItem'])) {
	
	$noItems = count($navbar);
	
	// Get, sanitize and validate incomings
	$args = array(
		'navbarlist' => array('filter'	=> FILTER_VALIDATE_INT, 
													'options'	=> array('min_range'=>1, 'max_range'=>$noItems)
													),
	);
	$inputs = filter_input_array(INPUT_POST, $args);
	$current = $inputs['navbarlist'];
	
	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 
	
	// Do whats to be done
	else if($current === false) {
		Throw new Exception("doDelItem, selected item ($current) is out of range.");
	} else if($current === null) {
		$output = "Select an item from the list and try again.";
	} else {
		for($i=$current;$i<$noItems;$i++) {
			$navbar[$i] = $navbar[$i+1];
		}
		unset($navbar[$noItems]);
		$_POST['navbarlist'] = 0;
		$items[$selectedLevel1]['nav'] = $navbar;
		$pp->UpdateConfiguration(array($configItem=>$items));
	}
}


// ------------------------------------------------------------------------------
//
// Move item up
//
else if(isset($_POST['doMoveItemUp'])) {

	$noItems = count($navbar);
	
	// Get, sanitize and validate incomings
	$args = array(
		'navbarlist' => array('filter'	=> FILTER_VALIDATE_INT, 
													'options'	=> array('min_range'=>1, 'max_range'=>$noItems)),
	);
	$inputs = filter_input_array(INPUT_POST, $args);
	$current = $inputs['navbarlist'];
	
	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 
	
	// Do whats to be done
	else if($current === false) {
		Throw new Exception("doMoveItemUp, selected item ({$current}) is out of range ({$noItems}).");
	} else if($current === null) {
		$output = "Select an item from the list and try again.";
	}	else if($current == 1) {
		// already at the top, do nothing
	} else {
		$tmp = $navbar[$current-1];
		$navbar[$current-1] = $navbar[$current];
		$navbar[$current] = $tmp;
		$_POST['navbarlist'] = $current-1;
		$items[$selectedLevel1]['nav'] = $navbar;
		$pp->UpdateConfiguration(array($configItem=>$items));
	}
}


// ------------------------------------------------------------------------------
//
// Move selected item down in the list. Item must be selected.
//
else if(isset($_POST['doMoveItemDown'])) {

	$noItems = count($navbar);
	
	// Get, sanitize and validate incomings
	$args = array(
		'navbarlist' => array('filter'	=> FILTER_VALIDATE_INT, 
													'options'	=> array('min_range'=>1, 'max_range'=>$noItems)),
	);
	$inputs = filter_input_array(INPUT_POST, $args);
	$current = $inputs['navbarlist'];
	
	// Check if logged in as admin
	if (!$pp->uc->IsAdministrator()) {
		$pp->ReloadPageAndRemember(array("output"=>"You must be logged in as administrator to do this.", "output-type"=>"error"));		
	} 
	
	// Do whats to be done
	else if($current === false) {
		Throw new Exception("doMoveItemDown, selected item ({$current}) is out of range ({$noItems}).");
	} else if($current === null) {
		$output = "Select an item from the list and try again.";
	}	else if($current == $noItems) {
		// already at the bottom, do nothing
	} else {
		$tmp = $navbar[$current+1];
		$navbar[$current+1] = $navbar[$current];
		$navbar[$current] = $tmp;
		$_POST['navbarlist'] = $current+1;
		$items[$selectedLevel1]['nav'] = $navbar;
		$pp->UpdateConfiguration(array($configItem=>$items));
	}
}


// ------------------------------------------------------------------------------
//
// Create a SELECT/OPTION list of the current navbar
//
$current = isset($_POST['navbarlist']) ? strip_tags($_POST['navbarlist']) : 0;
$select = "";
$size = count($navbar);
$select = "<select size=12 class=span-5 name=navbarlist onclick='form.submit();'>";
foreach($navbar as $key => $val) {
	$selected = $key == $current ? "selected " : "";
	$select .= "<option value='{$key}' {$selected}>{$val['text']}</option>";
}
$select .= "</select>";


// ------------------------------------------------------------------------------
//
// Show form for selected item, if any
//
$editItem = "<p>Select an item to edit it.</p>";
if($current) {

	$editItem = <<<EOD
<p>
	<label>Text:</label><br>
	<input type=text class=text name=text value="{$navbar[$current]['text']}">
</p>
<p>
	<label>Link:</label><br>
	<input type=text class=text name=url value="{$navbar[$current]['url']}">
</p>
<p>
	<label>Title:</label><br>
	<input type=text class=text name=title value="{$navbar[$current]['title']}">
</p>
<p>
	<input type=submit name=doSaveItem value='Save' {$disabled}>
	<input type=reset value='Reset'>
</p> 
<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>
EOD;
} 


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Configure navigation menus</h1>
<p>Define the items on the navigational menus of the site.</p>
<form action='?p={$p}' method=post>
	<fieldset>
		<!-- <legend></legend> -->
		<fieldset class=span-5>
			<legend>Menu</legend>
			<p>
				{$selectLevel1}<br>
				{$select}
			</p>
			
			<p class=clear>
				<input type=submit name=doAddItem title="Add a new item" value='+' {$disabled}>
				<input type=submit name=doDelItem title="Remove an item from the list" value='-' {$disabled}>
				<input type=submit name=doMoveItemUp title="Move item up" value='&uarr;' {$disabled}>
				<input type=submit name=doMoveItemDown title="Move item down" value='&darr;' {$disabled}>
			</p>
		</fieldset>
		
		<fieldset class="span-10 last">
			<legend>Menu item</legend>
			{$editItem}
		</fieldset>
			
		<p class=right><output class="span-6 {$remember['output-type']}">{$remember['output']}</output></p>
	</fieldset>
</form>
EOD;

