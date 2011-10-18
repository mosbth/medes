<?php
/**
 * Template helpers
 *
 * Functions, classes, variables and etc to aid in template files. This file is included by
 * CPrinceOfPersia, if it exists, just before including the template files.
 *
 * @package MedesCore
 */

/**
 * Dynamically change the content width depending on sidebar-content exists.
 */
function core_CalculateContentWidth(&$hasSidebar1, &$hasSidebar2, &$classContent, &$classSidebar1, &$classSidebar2) {
	global $pp;
	
	$hasSidebar1 = $pp->ViewExistsForRegion('sidebar1');
	$hasSidebar2 = $pp->ViewExistsForRegion('sidebar2');

	if($hasSidebar1 && $hasSidebar2) {
		$classContent="span-16 border";
		$classSidebar1="span-4 border";
		$classSidebar2="span-4 last";
	} else if($hasSidebar1) {
		$classContent="span-19 last";
		$classSidebar1="span-4 colborder";
		$classSidebar2=null;
	} else if($hasSidebar2) {
		$classContent="span-18 colborder";
		$classSidebar1=null;
		$classSidebar2="span-5 last";
	}
}