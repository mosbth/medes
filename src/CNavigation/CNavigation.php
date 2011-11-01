<?php
/**
 * Create menus for navigation.
 * 
 * @package MedesCore
 */
class CNavigation {
	
	/**
	 * Constructor. 
	 * @param array $array array of arrays containing text, url and active status
	 * @para boolean $list present menu as list
	 * @param string $id id of nav-element
	 * @param string $class class of nav-element
	 */
	public static function GenerateMenu($array, $list=false, $id=null, $class=null) {
		global $pp;
		$id 		= isset($id) ? "id='$id' " : null;
		$class 	= isset($class) ? "class='$class' " : null;

		$html = "<nav {$id}{$class}>\n";
		if($list) {
			$html .= "\t<ul>\n";
		}
		
		foreach($array as $key=>$value) {
			if($list) {
				$html .= "\t\t<li>";
			} else {
				$html .= "\t";
			}
			
      $classes = ' class="';
			if(isset($value['active'])) {
				$classes .= "active";
			}
			if(isset($value['class'])) {
				$classes .= " {$value['class']}";
			}
      $classes .= '"';

			$title = '';
			if(isset($value['title']))
				$title = " title=\"" . t($value['title']) . "\"";
			
			if(isset($value['href']))
				$html .= "<a href=\"" . $pp->req->CheckUrl($value['href']) . "\"{$title}{$classes}>{$value['text']}</a>";
			else
				$html .= "<a{$title}{$classes}>" . t($value['text']) . "</a>";

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
