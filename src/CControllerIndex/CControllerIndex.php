<?php
/**
 * Standard controller to implement the first standard home page.
 * 
 * @package MedesCore
 */
class CControllerIndex implements IController {

	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
		$pp = CPrinceOfPersia::GetInstance();
		$pp->AddView(new CView(array('html'=>'<h1>Index Controller</h2><p>Welcome!</p>')));
	}


} // End of class
