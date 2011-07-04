<?php
/**
 * Controller for managin canonical urls.
 * 
 * @package MedesCore
 */
class CCtrl4CanonicalUrl implements IController {

	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
		$pp = CPrinceOfPersia::GetInstance();
		$pp->AddView(new CView('<h1>Canonical Url Controller</h1><p>Welcome!</p>'));
	}


	/**
 	 * Display all items in table, ready to edit.
	 */
	public function EditShowAll() {	
		global $pp;
		
		$c = new CCanonicalUrl();
		$all = $c->ListAll();
		
		$html = "<h1>All Canonical Urls</h1>\n<table>\n<tr><th>" . t('Canonical') . "</th><th>" . t('Real') . "</th><th>" . t('Actions') . "</th></tr>\n";
		foreach($all as $val) {
			//$view = "<a href='" . $pp->req->CreateUrlToControllerAction(null, 'view', $val['key']) . "'>" . t('view') . "</a> ";
			$edit = "<a href='" . $pp->req->CreateUrlToControllerAction(null, 'edit', $val['id']) . "'>" . t('edit') . "</a> ";
			$html .= "<tr><td>{$val['url']}</td><td>{$val['real_url']}</td><td>{$edit}</td></tr>\n";
		}
		$html .= "</table>\n";
		
		$pp->pageTitle = t('All urls: ');
		$pp->AddView(new CView(), 0, 'sidebar2');
		$pp->AddView(new CView($html));		
	}


	/**
 	 * Get page in edit mode.
	 */
	public function Edit() {	
		global $pp;

		if(!isset($pp->req->args[0])) {
			$this->EditShowAll();
			return;
		}

		$f = new CForm();
		$f->id = 'mds-form-canurl-edit';
		$f->class = 'mds-form-canurl-edit';
		$f->legend = 'legend';
		$f->elements = array(
			'id' => array(
				'type' => 'hidden',
				'name' => 'id',
			),
			'canurl' => array(
				'label' => 'Canonical url:',
				//'type' => 'text',
				'class' => 'text',
				'name' => 'canurl',
				'mandatory' => true,
			),
			'realurl' => array(
				'label' => 'Real url:',
				//'type' => 'text',
				'class' => 'text',
				'name' => 'realurl',
				'mandatory' => true,
			),
		);
		$f->actions = array(
  		'save' => array(
				'type' => 'submit',
				'name' => 'doSave',
				'value' => 'Save',
				'disabled' => !$pp->uc->IsAuthenticated(),
				'callback' => array($this, 'DoSave'),
			),
			'reset' => array(
				'type' => 'reset',
			),
  		'delete' => array(
				'type' => 'submit',
				'name' => 'doDelete',
				'value' => 'Delete',
				'disabled' => !$pp->uc->IsAuthenticated(),
				'callback' => array($this, 'DoDelete'),
			),
  		'create' => array(
				'type' => 'submit',
				'name' => 'doCreate',
				'value' => 'Create',
				'disabled' => !$pp->uc->IsAuthenticated(),
				'callback' => array($this, 'DoCreate'),
			),
		);
		$f->CheckDoSubmitted();
		
		$c = new CCanonicalUrl();
		if(!$c->LoadById($pp->req->args[0])) {
			$pp->AddFeedbackError(t('Canonical url does not exists.'));
			$pp->req->RedirectTo('error', 'code404');
		}
		
		$f->SetValue('id', $c->GetId());
		$f->SetValue('canurl', sanitizeHtml($c->GetCanUrl()));
		$f->SetValue('realurl', sanitizeHtml($c->GetRealUrl()));
		
		$pp->pageTitle = t('Edit Canonical Url: ') . sanitizeHtml($c->GetCanUrl());
		$pp->AddView(new CView(), 0, 'sidebar2');
		$pp->AddView(new CView("<h1>Edit Canonical Url:</h1>" . $f->GetHTML()));
	}


	/**
	 * Save item.
	 */
	public function DoSave($form) {
		global $pp;
		$c = new CCanonicalUrl();
		if(!$c->LoadById($form->GetValue('id'))) {
			$pp->AddFeedbackError(t('Canonical url does not exists.'));
			$pp->req->RedirectTo('error', 'code404');
		}
		$c->SetCanUrl($form->GetValue('canurl'));
		$c->SetRealUrl($form->GetValue('realurl'));
		$c->Save();
		$pp->req->RedirectTo(null, 'edit', $c->GetId());
	}


	/**
	 * Create item.
	 */
	public function DoCreate($form) {
		global $pp;
		$c = new CCanonicalUrl();
		$c->SetCanUrl($form->GetValue('canurl'));
		$c->SetRealUrl($form->GetValue('realurl'));
		$c->Create();
		$pp->req->RedirectTo(null, 'edit', $c->GetId());
	}


	/**
	 * Delete item.
	 */
	public function DoDelete($form) {
		global $pp;
		$c = new CCanonicalUrl();
		if(!$c->LoadById($form->GetValue('id'))) {
			$pp->AddFeedbackError(t('Canonical url does not exists.'));
			$pp->req->RedirectTo('error', 'code404');
		}
		$c->Delete();
		$pp->req->RedirectTo(null, 'edit');
	}


} // End of class
