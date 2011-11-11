<?php
/**
 * Controller to help with theme creation and testing.
 * 
 * @package MedesCore
 */
class CCtrl4Theme implements IController {

	/**
	 * A reference to current CPrinceOfPersia
   * @var CPrinceOfPersia
   */
	private $pp;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->pp = CPrinceOfPersia::GetInstance();
		$pp = &$this->pp;
		
		$nav = array(
			array('text'=>'1 column', 'href'=>$pp->req->CreateUrlToControllerAction(),),
			array('text'=>'Sidebar1', 'href'=>$pp->req->CreateUrlToControllerAction(null, 'sidebar1'),),
			array('text'=>'Sidebar2',	'href'=>$pp->req->CreateUrlToControllerAction(null, 'sidebar2'),),
			array('text'=>'Sidebar1&2',	'href'=>$pp->req->CreateUrlToControllerAction(null, 'sidebar12'),),
		);
		$html = '<h1>Theme helper</h1><p>Here is useful information for a theme developer and tester, and maybe e menu (bar) of the options.</p>';	
		$html .= CNavigation::GenerateMenu($nav, $pp->pageUseListForMenus, 'mds-nav-theme', 'mds-nav-tabs');	
		$pp->AddView(new CView(array('html'=>$html, -2)));
		
		$nav = array();
		foreach($pp->cfg['config-db']['theme']['templates'] as $key=>$val) {
			$nav[] = array('text'=>"$key ($val)", 'href'=>$pp->req->CreateUrlToControllerAction(null, 'template', 'key', $key));
		}
		$html = '<br/><p>Change what template is used when rendering the page.</p>';	
		$html .= CNavigation::GenerateMenu($nav, $pp->pageUseListForMenus, 'mds-nav-theme', 'mds-nav-tabs');	
		$pp->AddView(new CView(array('html'=>$html, -1)));
	}
	
	
	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
	}


	/**
 	 * Action to change the template file used when rendering the page.
	 */
	public function Template() {	
		$pp = &$this->pp;
		$key = isset($pp->req->params['key']) ? $pp->req->params['key'] : null;
		$pp->SetTemplate($key);
	}


	/**
 	 * Action to display sidebar.
	 */
	public function Sidebar1() {	
		$pp = &$this->pp;
		$pp->AddView(new CView(array('html'=>'<h2>1</h2>')), 0, 'sidebar1');
	}


	/**
 	 * Action to display sidebar.
	 */
	public function Sidebar2() {	
		$pp = &$this->pp;
		$pp->AddView(new CView(array('html'=>'<h2>2</h2>')), 0, 'sidebar2');
	}


	/**
 	 * Action to display sidebar.
	 */
	public function Sidebar12() {	
		$this->Sidebar1();
		$this->Sidebar2();
	}


} // End of class
