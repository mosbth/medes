<?php
/**
 * A controller helping with debugging output, useful during development.
 * 
 * @package MedesCore
 */
class CDebug implements IController {

	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
		$pp = CPrinceOfPersia::GetInstance();
		
		$v = new CView();
		$v->AddStatic('<h1>Index Controller</h2><p>Welcome!</p>');
		$pp->AddView($v);
	}


} // End of class
