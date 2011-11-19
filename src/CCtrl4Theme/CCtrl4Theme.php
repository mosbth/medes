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
		$pp->AddView(new CView($html));
		
		$nav = array();
		foreach($pp->cfg['config-db']['theme']['templates'] as $key=>$val) {
			$nav[] = array('text'=>"$key ($val)", 'href'=>$pp->req->CreateUrlToControllerAction(null, 'template', 'key', $key));
		}
		$html = '<br/><p>Change what template is used when rendering the page.</p>';	
		$html .= CNavigation::GenerateMenu($nav, $pp->pageUseListForMenus);	
		$pp->AddView(new CView($html));
		
		$nav = array(
			array('text'=>'Show regions', 'href'=>$pp->req->CreateUrlToControllerAction(null, 'regions'),),
			array('text'=>'with grid', 'href'=>$pp->req->CreateUrlToControllerAction(null, 'regions', 'grid', 'show'),),
		);
		$html = '<br/><p>Display all regions, with or without the grid.</p>';	
		$html .= CNavigation::GenerateMenu($nav, $pp->pageUseListForMenus);	
		$pp->AddView(new CView($html));

		$nav = array();
		foreach($pp->cfg['config-db']['theme']['stylesheets'] as $key=>$val) {
		  if(isset($val['enabled']) && !$val['enabled']) {
  			$nav[] = array('text'=>"{$val['file']}", 'href'=>$pp->req->CreateUrlToControllerAction(null, 'stylesheet', 'key', urlencode($val['file'])));
      }
		}
		$html = '<br/><p>If this theme have disabled stylesheets those will be available here for tryout:</p>';	
		$html .= CNavigation::GenerateMenu($nav, $pp->pageUseListForMenus);	
		$pp->AddView(new CView($html));
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


	/**
 	 * Action to display all regions.
	 */
	public function Regions() {
	  global $pp;
		if(isset($pp->req->params['grid'])) {
		  $pp->pageStyle .= "#mds-header-area,#mds-promoted,#mds-triptych-area,#mds-footer-area,#mds-main-area{background: url(theme/core/img/grid.png);}\n";
		}
	  foreach($pp->cfg['config-db']['theme']['regions'] as $val) {
		  $pp->AddView(new CView(array('html'=>"<span style='position:relative;z-index:2;background:yellow;'>Region=$val</span>")), -99, $val);
		  $pp->pageStyle .= "#mds-{$val}{background-color:hsla(120, 100%, 75%, 0.3);min-height:50px;}\n";
	  }
	  $pp->pageStyle .= "#mds-top-left, #mds-top-right{min-height:0px;}\n";
	  $pp->pageStyle .= "#mds-header{min-width:300px;}\n";
	}


	/**
 	 * Action to display sidebar.
	 */
	public function Stylesheet() {	
	  global $pp;
		echo urldecode($pp->req->params['key']);
	}


} // End of class
