<?php
// ===========================================================================================
//
// File: CNavigation.php
//
// Description: ake1 will write this
//
// Author: Rickard Gimerstedt
//
// History:
// 2010-10-28: Created
//

class CNavigation {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
	
	// ------------------------------------------------------------------------------------
	//
	// Public internal variables
	//
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	protected function __construct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// One-liner on what the method does
	//
	// $array = array of arrays containing text, url and active status
	// $list = present menu as list
	// $class = class of nav-element
	//
	public static function GenerateMenu($array, $list, $class){
		$html = "<nav class=\"{$class}\">\n";
		if($list) {
			$html .= "\t<ul>\n";
		}
		
		foreach($array as $key=>$value) {
			if($list) {
				$html .= "\t\t<li>";
			} else {
				$html .= "\t";
			}
			
			$active = '';
			if(isset($value['active'])) {
				$active = " class=active";
			}
			
			$html .= "<a href=\"{$value['url']}\"{$active}>{$value['text']}</a>";
			if($list) {
				$html .= "</li>\n";
			} else {
				$html .= "\n";
			}
		}
		
		if($list) {
			$html .= "\t</ul>\n";
		}
		$html .= "</nav>\n";
		
		return $html;
	}

	
}
