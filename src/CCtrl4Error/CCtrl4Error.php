<?php
/**
 * Controller for displaying error messages and error pages.
 * 
 * @package MedesCore
 */
class CCtrl4Error implements IController {

	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
		$pp = CPrinceOfPersia::GetInstance();
		$pp->AddView(new CView('<h1>Error Controller</h1><p>Welcome!</p>'));
	}


	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Code404() {	
		global $pp;
		$feedback	= $pp->GetHTMLForFeedback();
		$pp->AddView(new CView("<h1>Error Controller</h1>{$feedback}<p>404</p>"));
		$pp->AddView(new CView(array('html'=>'<h2>$pp->req</h2><pre>' . print_r($pp->req, true) . '</pre>')));
	}


} // End of class
