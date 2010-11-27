<?php

// ------------------------------------------------------------------------------
//
// Define global settings
//
$output = '';
$outputItem = '';
$pp = CPrinceOfPersia::GetInstance();
$configKey = 'relatedsites';
$menu = $pp->config[$configKey];


// ------------------------------------------------------------------------------
//
// Save details on an item
//
if(isset($_POST['doSaveItem'])) {
	
	// Get and validate the incoming parameters
	$current 				= isset($_POST['menulist']) ? strip_tags($_POST['menulist']) : 0;
	$item['text'] 	= isset($_POST['text']) ? strip_tags($_POST['text']) : "";
	$item['url'] 		= isset($_POST['url']) ? strip_tags($_POST['url']) : "";
	$item['title'] 	= isset($_POST['title']) ? strip_tags($_POST['title']) : "";

	// Needs to have an item selected to be able to save it
	if(!$current) {
		Throw new Exception("Item is not selected and con not be saved. This should not happen. Report this as an error.");		
	}	

	// Save the information
	else {
		$menu[$current] = $item;
		$pp->UpdateConfiguration(array($configKey=>$menu));
		$outputItem = "The information was saved.";
	}
}


// ------------------------------------------------------------------------------
//
// Add new item
//
else if(isset($_POST['doAddItem'])) {
	
	// Get, sanitize and validate incomings
	$noItems = count($menu);	
	$args = array(
		'menulist' => array('filter'	=> FILTER_VALIDATE_INT, 
												'options'	=> array('default'=>$noItems, 'min_range'=>1, 'max_range'=>$noItems)
												),
	);
	$inputs = filter_input_array(INPUT_POST, $args);
	//$current = $inputs['menulist']; // http://bugs.php.net/50632 fails to set default value when POST is not set
	$current = $inputs['menulist'] === null ? $noItems : $inputs['menulist'];
	
	// Do whats to be done
	if($current === false) {
		Throw new Exception("doAddItem, selected item ($current) is out of range.");
	} else {
		$newItem['text'] 	= "new item";
		$newItem['url'] 	= "medes/doc/template.php";
		$newItem['title'] = "new item title";

		// Move all items +1 from $current
		for($i=$noItems;$i>$current;$i--) {
			$menu[$i+1] = $menu[$i];
		}
	
		// Add item and save
		$menu[$current+1] = $newItem;
		$_POST['menulist'] = $current+1;
		$pp->UpdateConfiguration(array($configKey=>$menu));
	}
}


// ------------------------------------------------------------------------------
//
// Delete item
//
else if(isset($_POST['doDelItem'])) {
	
	// Get, sanitize and validate incomings
	$noItems = count($menu);
	$args = array(
		'menulist' => array('filter'	=> FILTER_VALIDATE_INT, 
												'options'	=> array('min_range'=>1, 'max_range'=>$noItems)
												),
	);
	$inputs = filter_input_array(INPUT_POST, $args);
	$current = $inputs['menulist'];
	
	// Do whats to be done
	if($current === false) {
		Throw new Exception("doDelItem, selected item ($current) is out of range.");
	} else if($current === null) {
		$output = "Select an item from the list and try again.";
	} else {
		for($i=$current;$i<$noItems;$i++) {
			$menu[$i] = $menu[$i+1];
		}
		unset($menu[$noItems]);
		$_POST['menulist'] = 0;
		$pp->UpdateConfiguration(array($configKey=>$menu));
	}
}


// ------------------------------------------------------------------------------
//
// Move item up
//
else if(isset($_POST['doMoveItemUp'])) {

	// Get, sanitize and validate incomings
	$noItems = count($menu);	
	$args = array(
		'menulist' => array('filter'	=> FILTER_VALIDATE_INT, 
												'options'	=> array('min_range'=>1, 'max_range'=>$noItems)
												),
		);
	$inputs = filter_input_array(INPUT_POST, $args);
	$current = $inputs['menulist'];
	
	// Do whats to be done
	if($current === false) {
		Throw new Exception("doMoveItemUp, selected item ($current) is out of range.");
	} else if($current === null) {
		$output = "Select an item from the list and try again.";
	}	else if($current == 1) {
		// already at the top, do nothing
	} else {
		$tmp = $menu[$current-1];
		$menu[$current-1] = $menu[$current];
		$menu[$current] = $tmp;
		$_POST['menulist'] = $current-1;
		$pp->UpdateConfiguration(array($configKey=>$menu));
	}
}


// ------------------------------------------------------------------------------
//
// Move selected item down in the list. Item must be selected.
//
else if(isset($_POST['doMoveItemDown'])) {

	// Get, sanitize and validate incomings
	$noItems = count($menu);	
	$args = array(
		'menulist' => array('filter'	=> FILTER_VALIDATE_INT, 
												'options'	=> array('min_range'=>1, 'max_range'=>$noItems)),
												);
	$inputs = filter_input_array(INPUT_POST, $args);
	$current = $inputs['menulist'];
	
	// Do whats to be done
	if($current === false) {
		Throw new Exception("doMoveItemDown, selected item ($current) is out of range.");
	} else if($current === null) {
		$output = "Select an item from the list and try again.";
	}	else if($current == $noItems) {
		// already at the bottom, do nothing
	} else {
		$tmp = $menu[$current+1];
		$menu[$current+1] = $menu[$current];
		$menu[$current] = $tmp;
		$_POST['menulist'] = $current+1;
		$pp->UpdateConfiguration(array($configKey=>$menu));
	}
}


// ------------------------------------------------------------------------------
//
// Create a SELECT/OPTION list of the current menu
//
$current = isset($_POST['menulist']) ? strip_tags($_POST['menulist']) : 0;
$size = count($menu);
$select = "<select size={$size} name=menulist onclick='form.submit();'>";
foreach($menu as $key => $val) {
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
<label>Text:<input type=text name=text value="{$menu[$current]['text']}"></label>
<label>Link:<input type=text name=url value="{$menu[$current]['url']}"></label>
<label>Title:<input type=text name=title value="{$menu[$current]['title']}"></label>
<div class='buttonbar'>
	<input type=submit name=doSaveItem value='Save'>
</div> 
<output>{$outputItem}</output> 
EOD;
} 


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Set navigation bar</h1>
<p>Define the items on the navigation bar (main menu) of the site.</p>
<form action='?p={$p}' method=post>
	<fieldset class='std type21'>
		<!-- <legend></legend> -->
		<div class=wrap>
			<div class=wrap1>
				<label>Items on navigation bar:{$select}</label>
				<div class='buttonbar'>
					<input type=submit name=doAddItem title="Add a new item" value='+'>
					<input type=submit name=doDelItem title="Remove an item from the list" value='-'>
					<input type=submit name=doMoveItemUp title="Move item up" value='&uarr;'>
					<input type=submit name=doMoveItemDown title="Move item down" value='&darr;'>
				</div> 
			</div>
			<div class=wrap2>{$editItem}</div>
			<output>{$output}</output> 
		</div>
	</fieldset>
</form>
EOD;

