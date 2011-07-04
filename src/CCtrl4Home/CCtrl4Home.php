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
		$pp = CPrinceOfPersia::GetInstance();
		$pp->AddView(new CView(array('html'=>'<h1>Index Controller</h1><p>Welcome!</p>')));
	}


} // End of class
