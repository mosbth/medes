<?php
/**
 * Admin control panel.
 * 
 * @package MedesCore
 */
class CCtrl4AdminControlPanel implements IController {

	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
		global $pp;

		$pp->AddView(new CView('<h1>Admin Control Panel</h1><p>Welcome!</p>'));
	}


} // End of class
