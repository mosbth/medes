<?php
/**
 * A controller helping with debugging output, useful during development.
 * 
 * @package MedesCore
 */
class CCtrl4Developer implements IController {

	/**
	 * Constructor
	 */
	public function __construct() {
		$header = '<h1>Developer information</h2><p>Here is useful information for a developer, and maybe e menu (bar) of the options.</p>';

		$nav = array(
			array('text'=>'Configuration', 'href'=>$pp->req->CreateUrlToControllerAction(null, 'config'),),
			array('text'=>'Request', 'href'=>$pp->req->CreateUrlToControllerAction(null, 'request'),),
			array('text'=>'$_SERVER', 'href'=>$pp->req->CreateUrlToControllerAction(null, 'server'),),
			array('text'=>'$_SESSION', 'href'=>$pp->req->CreateUrlToControllerAction(null, 'session'),),
			array('text'=>'Call controller method using arguments', 'href'=>$pp->req->CreateUrlToControllerAction(null, 'args'),),
		);
		$header .= CNavigation::GenerateMenu($nav, $pp->pageUseListForMenus, 'mds-nav-developer', 'mds-nav-developer mds-nav-tabs');	
		$pp->AddView(new CView($header));
	}
	
	
	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
	}


	/**
 	 * Action to print out the $pp cfg (configuration).
	 */
	public function Config() {	
		global $pp;
		$pp->AddView(new CView(array('html'=>'<h2>$pp->cfg</h2><pre>' . print_r($pp->cfg, true) . '</pre>')));
	}


	/**
 	 * Action to print out the request object.
	 */
	public function Request() {	
		global $pp;
		$pp->AddView(new CView(array('html'=>'<h2>$pp->req</h2><pre>' . print_r($pp->req, true) . '</pre>')));
	}


	/**
 	 * Action to print out $_SERVER.
	 */
	public function Server() {	
		global $pp;
		$pp->AddView(new CView(array('html'=>'<h2>$_SERVER[]</h2><pre>' . print_r($_SERVER, true) . '</pre>')));
	}


	/**
 	 * Action to print out $_SESSION.
	 */
	public function Session() {	
		global $pp;
		$pp->AddView(new CView(array('html'=>'<h2>$_SESSION[]</h2><pre>' . print_r($_SESSION, true) . '</pre>')));
	}


	/**
 	 * Action to take pass arguments to controller method through the url
	 */
	public function Args($v1=null, $v2=null, $v3=null) {	
		global $pp;
		$html = "<h2>Passing urlparts as methid arguments</h2><p>This controller method takes three arguments that can be passed through the url.</p>";
		$html .= "<a href='" . $pp->req->CreateUrlToControllerAction(null, 'args', 1) . "'>Passing one argument</a> | ";
		$html .= "<a href='" . $pp->req->CreateUrlToControllerAction(null, 'args', 1, 2) . "'>Passing two arguments</a> | ";
		$html .= "<a href='" . $pp->req->CreateUrlToControllerAction(null, 'args', 1, 2, 3) . "'>Passing three arguments</a>";
		$html .= "<pre>Argument 1 = " . (is_int($v1) ? $v1 : 'is not a number.');
		$html .= "Argument 2 = " . (is_int($v2) ? $v2 : 'is not a number.');
		$html .= "Argument 3 = " . (is_int($v3) ? $v3 : 'is not a number.');
		$html .= "</pre>";
		$pp->AddView(new CView($html));
	}


} // End of class
