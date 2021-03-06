<?php
/**
 * Standard controller to implement the first standard home page.
 * 
 * @package MedesCore
 */
class CCtrl4Home implements IController {

	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
		global $pp;

		if(isset($pp->cfg['config-db']['general']['home'])) {
      $pp->req->ForwardTo($pp->cfg['config-db']['general']['home']['href']);
      $pp->FrontControllerRoute();
		} else {
			$pp->AddView(new CView('<h1>Index Controller</h1><p>Welcome!</p>'));
		}
	}


} // End of class
