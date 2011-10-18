<?php
/**
 * Controller for ContentPage objects, created and edited by a user.
 * 
 * @package MedesCore
 */
class CCtrl4ContentPage implements IController {


	/**
 	 * Implementing interface IController. All controllers must have an index action.
	 */
	public function Index() {	
		global $pp;
		$this->EditShowAll();
	}


	/**
 	 * Create a new page by providing a key/name of the page.
	 */
	public function Create() {	
		global $pp;
    $pp->if->UserIsSignedInOrRedirectToSignIn();
		
		$f = new CForm();
		$f->id = 'mds-form-cpage-create';
		$f->class = 'mds-form-cpage-create';
		$f->elements = array(
			'key' => array(
				'label' => 'Page key:',
				'type' => 'text',
				'class' => 'text',
				'name' => 'key',
				//'mandatory' => true,
			),
		);
		$f->actions = array(
			'create' => array(
				'type' => 'submit',
				'name' => 'doCreate',
				'value' => 'Create',
				'disabled' => !$pp->uc->IsAuthenticated(),
				'callback' => array($this, 'DoCreateByKey'),
			),
		);
		$f->CheckDoSubmitted();
		
		$html = t("<h1>Create a new page</h1>\n<p>Enter a key for the page. The key is an internal name used when referencing the page.</p>");
		$html .= $f->GetHTML();

		$pp->AddView(new CView(), 0, 'sidebar2');
		$pp->AddView(new CView(array('html'=>$html)));
	}


	/**
	 * Create a new page with a defined key.
	 */
	public function DoCreateByKey($form) {
		global $pp;
    $pp->if->UserIsSignedInOrRedirectToSignIn();

		$key = isset($_POST['key']) ? $_POST['key'] : null;  
		if(!$key) throw new Exception(t('Missing key.'));

		$c = new CContentPage($_POST['key']);
		if(!$c->Exists()) {
			$c->SaveContent(t("<h1>New page</h1>\n\n<p>Edit this text to modify your custom page.</p>"), $key);
			header("Location: " . $pp->req->CreateUrlToControllerAction(null, 'edit', $key));
			exit;
		}
		
		$form->AddFeedbackAlert(t('Failed to create page. A page already exists with that key.'));
		header("Location: " . $pp->req->CreateUrlToControllerAction(null, 'create'));
		exit;
	}
	

	/**
 	 * View pages or a specified page.
	 */
	public function View() {	
		global $pp;
		$html = null;
		
		// Key exists, show one page
		if(!isset($pp->req->args[0])) {
		  $this->EditShowAll();
		  return;
		}

    // Display one page		
    $c = new CContentPage();
    if(!$c->LoadByKey($pp->req->args[0])) {
      $pp->AddFeedbackError(t('Page does not exists.'));
      $pp->FrontControllerRoute('error', 'code404');
      //header("Location: " . $pp->req->CreateUrlToControllerAction('error', 'code404'));
      //exit;	
    }
    $pp->SetPageTitle($c->GetTitle());
		$pp->AddView(new CView($c->GetFilteredContent()));
	}


	/**
 	 * Display all pages in table, ready to edit.
	 */
	public function EditShowAll() {	
		global $pp;
    $pp->if->UserIsSignedInOrRedirectToSignIn();
		
		$c = new CContentPage();
		$all = $c->ListAll();
		
		$html = "<h1>All pages</h1>\n<table>\n<tr><th>" . t('Name') . "</th><th>" . t('Title') . "</th><th>" . t('Actions') . "</th></tr>\n";
		foreach($all as $val) {
			$view = "<a href='" . $pp->req->CreateUrlToControllerAction(null, 'view', $val['key']) . "'>" . t('view') . "</a> ";
			$edit = "<a href='" . $pp->req->CreateUrlToControllerAction(null, 'edit', $val['key']) . "'>" . t('edit') . "</a> ";
			$html .= "<tr><td>{$val['key']}</td><td>{$val['title']}</td><td>{$view}{$edit}</td></tr>\n";
		}
		$html .= "</table>\n";
		
		$pp->pageTitle = t('All pages: ');
		$pp->AddView(new CView(), 0, 'sidebar2');
		$pp->AddView(new CView($html));		
	}


	/**
 	 * Get page in edit mode.
	 */
	public function Edit() {	
		global $pp;
    $pp->if->UserIsSignedInOrRedirectToSignIn();

		if(!isset($pp->req->args[0])) {
			$this->EditShowAll();
			return;
		}

		$f = new CForm();
		$f->id = 'mds-form-cpage-edit';
		$f->class = 'mds-form-cpage-edit';
		$f->legend = 'legend';
		$f->elements = array(
			'id' => array(
				'type' => 'hidden',
				'name' => 'id',
			),
			'key' => array(
				'label' => 'Name:',
				'type' => 'text',
				'class' => 'text',
				'name' => 'key',
				'mandatory' => true,
			),
			'title' => array(
				'label' => 'Title:',
				'type' => 'text',
				'class' => 'text',
				'name' => 'title',
				'mandatory' => true,
			),
			
/*			'can_url' => array(
				'label' => 'Canonical URL:',
				'type' => 'text',
				'class' => 'text',
				'name' => 'can_url',
			),
*/
			'content' => array(
				'label' => 'Content:',
				'type' => 'textarea',
				'class' => 'wide',
				'name' => 'content',
			),
			'filter' => array(
				'label' => 'Allowed content is:',
				'type' => 'select',
				'class' => 'wide',
				'name' => 'filter',
				'options' => array(
				  'text' => 'Plain Text',
				  'html' => 'HTML',
				  'php' => 'PHP',
				),
			),
		);
		$f->actions = array(
/*			'publish' => array(
				'type' => 'submit',
				'name' => 'doPublish',
				'value' => 'Publish',
				'disabled' => !$pp->uc->IsAuthenticated(),
				'callback' => array($this, 'DoPublish'),
			),
*/			'save' => array(
				'type' => 'submit',
				'name' => 'doSave',
				'value' => 'Save',
				'disabled' => !$pp->uc->IsAuthenticated(),
				'callback' => array($this, 'DoSave'),
			),
			'reset' => array(
				'type' => 'reset',
			),
		);
		$f->CheckDoSubmitted();
		
		$c = new CContentPage();
		if(!$c->LoadByKey($pp->req->args[0])) {
			$pp->AddFeedbackError(t('Page does not exists.'));
			$pp->FrontControllerRoute('error', 'code404');
			//header("Location: " . $pp->req->CreateUrlToControllerAction('error', 'code404'));
			//exit;	
		}
		
		//$content = $c->GetDraftContent();
		$f->SetValue('id', $c->GetId());
		$f->SetValue('key', sanitizeHtml($c->GetKey()));
		$f->SetValue('title', sanitizeHtml($c->GetTitle()));
		$f->SetValue('content', sanitizeHtml($c->GetContent()));
		$f->SetValue('filter', sanitizeHtml($c->GetFilter()));
		//$f->SetValue('can_url', sanitizeHtml($c->GetCanonicalUrl()));
		//$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";	
		//$statusBar = $this->GetStatusBar();
		//$details = $this->GetViewSideMenu();
		$statusBar = null;
		$details = null;
		
		$pp->pageTitle = t('Edit page: ') . sanitizeHtml($c->GetTitle());
		$pp->AddView(new CView(array('html'=>$details)), 0, 'sidebar2');
		$pp->AddView(new CView($f->GetHTML()));
	}


	/**
	 * Publish the page.
	 */
	public function DoPublish($form) {
		global $pp;
    $pp->if->UserIsSignedInOrRedirectToSignIn();
	}
	

	/**
	 * Save page.
	 */
	public function DoSave($form) {
		global $pp;
    $pp->if->UserIsSignedInOrRedirectToSignIn();
		
		$c = new CContentPage();		
		if(!$c->LoadById($form->GetValue('id'))) {
			$pp->AddFeedbackError(t('Page does not exists.'));
			$pp->req->RedirectTo('error', 'code404');
		}
		$c->SetTitle($form->GetValue('title'));
		$c->SetKey($form->GetValue('key'));
		//$c->SetCanonicalUrl($form->GetValue('can_url'));
		$c->SetContent($form->GetValue('content'));
		$c->SetFilter($form->GetValue('filter'));
		$c->Save();
		$pp->req->RedirectTo(null, 'edit', $c->GetKey());
	}














  // BELOW IS OBSOLETE, WILL BE REWRITTEN

	// ------------------------------------------------------------------------------------
	//
	// Save page
	//
	protected function DoActionSaveDraftPage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));

		$this->SaveDraftContent($_POST['content'], $_GET['p']);
		CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Page was saved as draft.", "output-type"=>"success"));
	}
	
	


	// ------------------------------------------------------------------------------------
	//
	// Get status bar with info about page. 
	//
	public function GetStatusBar($class="quiet small") {
		// Is user authenticated?
		$uc = CUserController::GetInstance();
		$isAuthenticated = $uc->IsAuthenticated();
		if(!$isAuthenticated) {
			return "";
		}
		
		// Is in editable mode o viewing mode
		$isInEditMode = isset($_GET['e']);

		// Get some values for page
		$hasDraft = $this->a->GetDraftContent();
		$published = $this->a->GetPublished();
		$created = $this->a->GetCreated();
		$modified = $this->a->GetModified();
		$deleted = $this->a->GetDeleted();
		
		// Format dates
		$tz = new DateTimeZone('UTC');
		$timeCreated = CPrinceOfPersia::FormatDateTimeDiff($created, $tz);
		$timeModified = CPrinceOfPersia::FormatDateTimeDiff($modified, $tz);
		$timeDeleted = CPrinceOfPersia::FormatDateTimeDiff($deleted, $tz);
		
		// Get current querystring
		//$qs = CPrinceOfPersia::GetQueryString();
		
		// Does this page really exists?
		if(is_null($this->a->GetId())) {
			return "<p class='{$class}'>This page does not exists, <a href='?p={$this->key}&amp;a=createPageByKey'>you may create it now</a>.</p>";
		}

		// Create html for the menu
		$html = "<p class='{$class}'>";
		if($deleted) {
			$html .= "Page was deleted {$timeDeleted} ago and exists in the wastebasket. ";
		} else {
			$html .= $published ? "Page is published. " : "Page is not yet published. ";
			$html .= $hasDraft ? "Draft exists. " : "";
			$html .= $modified ? "Last modified in {$timeModified}. " : "Created {$timeCreated} ago. ";
		}
		$html .= " Owner is " . $this->a->GetOwner() . ". ";
		$html .= $isInEditMode ? "<a href='?p={$this->key}'>View page</a>. ":"<a href='?p={$this->key}&amp;e'>Edit page</a>. ";
/*
		$html .= $isAuthenticated ? "<a href='?p={$this->key}&amp;a=newpage'>Create new page</a>. ":'';
		$html .= $isAuthenticated ? "<a href='?a=viewpages'>View all pages</a>. ":'';
		$restorePage = $isAuthenticated ? "<a href='?a=restorepage'>Restore this page</a> ":'';
		$deletePage = $isAuthenticated ? "<a href='?a=deletepage'>Delete this page</a>. ":'';
		$html .= $deleted ? "Page was deleted {$deleted} ({$restorePage}). " : $deletePage;
*/
		$html .= "</p>";
		
		return $html;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get page with standard html elements. 
	//
	public function GetViewSideMenu() {
		// Get some values for page
		$hasDraft = $this->a->GetDraftContent();
		$published = $this->a->GetPublished();
		$created = $this->a->GetCreated();
		$modified = $this->a->GetModified();
		$deleted = $this->a->GetDeleted();

		// Get datetimes
		$tz = new DateTimeZone('UTC');
		$timePublished = CPrinceOfPersia::FormatDateTimeDiff($published, $tz);
		$timeCreated = CPrinceOfPersia::FormatDateTimeDiff($created, $tz);
		$timeModified = CPrinceOfPersia::FormatDateTimeDiff($modified, $tz);		
		$timeDeleted = CPrinceOfPersia::FormatDateTimeDiff($deleted, $tz);

		// Create html for the details
		$details = "<h4>Details</h4><p>This page is named '{$this->key}'. <a href='?p={$this->key}&amp;a=renamePage'>Rename page</a>. ";
		$details .= "Owner is " . $this->a->GetOwner() . ".</p>";
		$details .= $modified ? "<p>Page created {$timeCreated} ago and last modified in {$timeModified}.</p>" : "<p>Page created {$timeCreated} ago.</p>";
		$details .= $published ? "<p>Page published {$timePublished} ago. <a href='?p={$this->key}&amp;e&amp;a=unpublishPage'>Unpublish page</a>.</p>" : "<p>Page is not yet published. <a href='?p={$this->key}&amp;e&amp;a=publishPage'>Publish page now</a>.</p>";
		$details .= "<p><a href='?p={$this->key}'>View page</a>.</p>";
		$details .= $hasDraft ? "<p>Draft exists, <a href='?p={$this->key}&amp;draft'>preview it</a>. <a href='?p={$this->key}&amp;e&amp;a=destroyDraftPage'>Destroy draft</a>.</p>" : "";
		$details .= $deleted ? "<p>Page was deleted {$timeDeleted} ago. <a href='?p={$this->key}&amp;e&amp;a=restorePage'>Restore page from wastebasket</a>.</p>" : "<p><a href='?p={$this->key}&amp;e&amp;a=deletePage'>Delete page to wastebasket</a>.</p>";
		$details .= "<h4>All pages</h4>";
		$details .= "<p><a href='?a=createPage'>Create new page</a>.</p>";
		$details .= "<p><a href='?a=viewPages'>View all pages</a>.</p>";
		$details .= "<p>There exists x published pages. <a href='?a=viewPages&amp;published'>View</a>.</p>";
		$details .= "<p>There exists x unpublished pages. <a href='?a=viewPages&amp;unpublished'>View</a>.</p>";
		$details .= "<p>There exists x deleted pages in the wastebasket. <a href='?a=viewPages&amp;deleted'>View</a>.</p>";
		return $details;
	}


	// ------------------------------------------------------------------------------------
	//
	// Interface IActionHandler
	// Manage _GET and _POST requests and redirect or return the resulting html. 
	//
	public function ActionHandler() {
		// Check what _POST contains
		if(isset($_POST['doSaveDraftPage'])) return $this->DoActionSaveDraftPage();
		if(isset($_POST['doPublishPage'])) return $this->DoActionSaveAndPublishPage();
		if(isset($_POST['doRenamePage'])) return $this->DoActionRenamePage();

		// Check what _GET contains
		$a = isset($_GET['a']) ? $_GET['a'] : null; // Do some action with the page 
		$e = isset($_GET['e']); // Edit page
		$p = isset($_GET['p']); // Display page

		if($a) {
			switch($a) {
				case 'viewNewPage': 			return $this->DoActionViewNewPage(); break;
				case 'createPage': 				return $this->DoActionCreatePage(); break;
				case 'createPageByKey': 	return $this->DoActionCreatePageByKey(); break;
				case 'renamePage': 				return $this->DoActionViewRenamePage(); break;
				case 'publishPage': 			return $this->DoActionPublishPage(); break;
				case 'unpublishPage': 		return $this->DoActionUnpublishPage(); break;
				case 'deletePage': 				return $this->DoActionDeletePage(); break;
				case 'restorePage': 			return $this->DoActionRestorePage(); break;
				case 'destroyDraftPage': 	return $this->DoActionDestroyDraftPage(); break;
				case 'viewPages': 				return $this->DoActionViewPages(); break;
				default: echo "403";
			}
		} else if($e) {
			return $this->DoActionEditPage();
		} else if($p) {
			return $this->DoActionViewPage();
		} else {
			return "<p>404 eller förklaring till denna sidan, olika om inloggad eller ej, kan bli front till att söka bland artiklar, en pagecp, även som a=search&amp;q=searchstring, visa senast uppdaterade och visa ens egna artiklar .</p>";		
		}
	}


	public function Test() {	
		global $pp;
		
		$p = new ContentPage();
		
/*		
		$this->key = "template-page";
		$this->title = "Template using CContentPage to store content in database";
		$this->SaveContent($content);
*/

		$html = "<h1>ContentPage objects</h1><p>Welcome!</p>";
		
		$pp->AddView(new CView(array('html'=>$html)));
	}


} // End of class
