<?php
/**
 * A controller for the user. Aids with login, logout and updating the user profile.
 * 
 * @package MedesCore
 */
class CCtrl4User implements IController {

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
	}
	
	
	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
	}


	/**
 	 * Action to login.
	 */
	public function Login() {	
		$pp = &$this->pp;
		
		// Form for login
		$f = new CForm();
		$f->id = 'mds-form-login';
		$f->class = 'mds-form-login';
//		$f->action = $pp->req->CreateUrlToControllerAction('user', 'loginp');		
		$f->elements = array(
			'user' => array(
				'label' => 'User:',
				'type' => 'text',
				'class' => 'text',
				'name' => 'user',
				//'mandatory' => true,
			),
			'password' => array(
				'label' => 'Password:',
				'type' => 'password',
				'class' => 'text',
				'name' => 'password',
				//'mandatory' => true,
			),
		);
		$f->actions = array(
			'login' => array(
				'type' => 'submit',
				'name' => 'doLogin',
				'value' => 'Login',
				'disabled' => $pp->uc->IsAuthenticated(),
				'callback' => array($this, 'DoLogin'),
			),
		);
		$f->CheckDoSubmitted();
		
		$html = t("<h1>Login</h1>\n<p>Login using your userid and password.</p>");
		$html .= $f->GetHTML();

		$pp->AddView(new CView(), 0, 'sidebar2');
		$v = new CView();
		$v->AddStatic($html);
		$pp->AddView($v);
	}


	/**
 	 * Action to perform the login.
	 */
	public function DoLogin($form) {	
		$pp = &$this->pp;
		$ret = $pp->uc->Login($_POST['user'], $_POST['password']);
		
		if($ret) {
			$form->AddFeedbackSuccess(t('You have logged in successfully.'));
		} else {
			$form->AddFeedbackAlert(t('You failed to login, check your account and password and try again.'));			
		}
		
		header("Location: " . $pp->req->CreateUrlToControllerAction(null, 'login'));
		exit;
	}
	

	/**
 	 * Action to logout.
	 */
	public function Logout() {	
		$pp = &$this->pp;
		
		// Form for logout
		$f = new CForm();
		$f->id = 'mds-form-logout';
		$f->class = 'mds-form-logout';
		$f->actions = array(
			'logout' => array(
				'type' => 'submit',
				'name' => 'doLogout',
				'value' => 'Logout',
				'disabled' => !$pp->uc->IsAuthenticated(),
				'callback' => array($this, 'DoLogout'),
			),
		);
		$f->CheckDoSubmitted();
		
		$html = t("<h1>Logout</h1>\n<p>Logout from this site.</p>");
		$html .= $f->GetHTML();

		$pp->AddView(new CView(), 0, 'sidebar2');
		$v = new CView();
		$v->AddStatic($html);
		$pp->AddView($v);
	}


	/**
 	 * Action to logout.
	 */
	public function DoLogout($form) {	
		$pp = &$this->pp;
		$pp->uc->Logout();
		$form->AddFeedbackSuccess(t('You have successfully logged out from this site.'));

		header("Location: " . $pp->req->CreateUrlToControllerAction(null, 'logout'));
		exit;
	}


	/**
 	 * Action to view user settings.
	 */
	public function Settings() {	
		$pp = &$this->pp;
		
		$html = t("<h1>Settings</h1>\n<p>Change user and site settings for this account.</p>");

		$pp->AddView(new CView(), 0, 'sidebar2');
		$v = new CView();
		$v->AddStatic($html);
		$pp->AddView($v);
	}


} // End of class
