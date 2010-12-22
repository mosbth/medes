<?php
// ===========================================================================================
//
// File: CContentPage.php
//
// Description: Creating content stored in the database, based on a key and content of an 
// article. An easy way to create dynamic webpages that are stored in the database and 
// can be edited online.
//
// Author: Mikael Roos
//
// History:
// 2010-12-14: Created
//

class CContentPage extends CContent implements IInstallable, IActionHandler {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $key;
	protected $title;
	const TYPE = "CContentPage";
	
	
	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aKey="") {
		parent::__construct();
		$this->key = $aKey;
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {
		parent::__destruct();
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Install. 
	//
  public function Install() {
		$content = <<<EOD
<article>
<h1>Template page with content stored in database</h1>
<p>This is a page stored in the database. Login to edit the page content.
</p>
<p>You can edit, delete or create new pages.
</p>
<p>Pages are a great way of quickly adding content to your website.
</p>
<p>Feel free to try it out by editing this text.
</p>
</article>
EOD;
		$this->key = "template-page";
		$this->title = "Template using CContentPage to store content in database";
		$this->SaveContent($content);
	}


	// ------------------------------------------------------------------------------------
	//
	// Set the key of the page. 
	//
	public function SetKey($aKey) {
		$this->key = $aKey;
	}


	// ------------------------------------------------------------------------------------
	//
	// Save page content. 
	//
	public function SaveContent($aContent, $aKey=null) {
		if(!is_null($aKey)) {
			$this->key = $aKey;
		}
	
		if(empty($this->key)) {
			Throw new Exception(get_class() . " error: Saving content with empty key.");
		}
		
		$this->a->ClearCurrent();
		$this->a->SetKey($this->key);
		$this->a->SetType(self::TYPE);
		$this->a->SetTitle($this->title);
		$this->a->SetContent($aContent);
		$this->a->Save();
	}


	// ------------------------------------------------------------------------------------
	//
	// Get page content. 
	//
	public function GetContent($aKey=null) {
		if(isset($aKey)) {
			$this->key = $aKey;
		}
	
		if(empty($this->key)) {
			Throw new Exception(get_class() . " error: Getting content with empty key.");
		}
		
		$this->a->LoadByKey($this->key);
		return $this->a->GetContent();
	}


	// ------------------------------------------------------------------------------------
	//
	// Delete all pages. 
	//
	public function DeleteAll() {
		parent::DeleteAllByOwner(get_class());
	}


	// ------------------------------------------------------------------------------------
	//
	// Get menu to edit page. 
	//
	public function GetMenu($class="quiet small") {
		// Is user authenticated?
		$uc = CUserController::GetInstance();
		$isAuthenticated = $uc->IsAuthenticated();
		if(!$isAuthenticated) {
			return "";
		}
		
		// Is in editable mode o viewing mode
		$isInEditMode = isset($_GET['e']);

		// Get some values for page
		$published = $this->a->GetPublished();
		$created = $this->a->GetCreated();
		$modified = $this->a->GetModified();
		$deleted = $this->a->GetDeleted();
		
		// Get current querystring
		//$qs = CPrinceOfPersia::GetQueryString();
		
		// Does this page really exists?
		if(is_null($this->a->GetId())) {
			return "<p class='{$class}'>This page does not exists, <a href='?p={$this->key}&amp;a=newpage'>you may create it now</a>.</p>";
		}

		// Create html for the menu
		$html = "<p class='{$class}'>";
		$html .= $published ? "Page is published. " : "Page is not yet published. ";
		$html .= $modified ? "Last modified {$modified}. " : "Created {$created}. ";
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
	public function GetPage($class1="", $class2="") {
		$content = $this->GetContent();
		$menu = $this->GetMenu();
		$page = <<<EOD
<div class='{$class2}'>
{$menu}
</div>
<div class='{$class1}'>
{$content}
</div>
EOD;
		return $page;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get page in edit mode. 
	//
	public function GetPageAsForm() {
		$pp = CPrinceOfPersia::GetInstance();
		$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
		$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
		$content = $this->GetContent();
		$menu = $this->GetMenu();
		$page = <<<EOD
{$menu}
<form action='?p={$this->key}&amp;e' method=post>
	<fieldset>
		<legend>Edit page: {$this->key}</legend>
		<p class=right><output class="span-5 {$remember['output-type']}">{$remember['output']}</output></p>
		
		<p>
			<label for=input1>Content:</label><br>
			<textarea id=input1 class="wide" name=content>{$content}</textarea>
		</p>
		
		<p>
			<input type=submit name=doSavePage value='Save' {$disabled}>
			<input type=reset value='Reset'>
		</p>
		
	</fieldset>
</form>

EOD;
		return $page;
	}


	// ====================================================================================
	//
	//	Code below relates to the interface IActionHandler
	//

	// ------------------------------------------------------------------------------------
	//
	// Interface IActionHandler
	// Manage _GET and _POST requests and redirect or return the resulting html. 
	//
	public function ActionHandler() {
		// Check what _POST contains
		if(isset($_POST['doSavePage'])) $this->DoActionSavePage();

		// Check what _GET contains
		$a = isset($_GET['a']); // Do some action with the page 
		$e = isset($_GET['e']); // Edit page
		$p = isset($_GET['p']); // Display page
		
		if($a) {
			switch($a) {
				case 'newpage': 	return $this->DoActionNewPage(); break;
				default: 					echo "403";
			}
		} else if($e) {
			return $this->DoActionEditPage();
		} else if($p) {
			return $this->DoActionViewPage();
		} else {
			return "<p>404 eller förklaring till denna sidan, olika om inloggad eller ej, kan bli front till att söka bland artiklar, en pagecp, även som a=search&amp;q=searchstring, visa senast uppdaterade och visa ens egna artiklar .</p>";		
		}
	}

	
	// ------------------------------------------------------------------------------------
	//
	// View a page
	//
	protected function DoActionViewPage() {
		if(empty($_GET['p'])) Throw new Exception(get_class() . " error: DoActionViewPage with empty key.");

		$this->key = $_GET['p'];
		return $this->GetPage();
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Edit a page
	//
	protected function DoActionEditPage() {
		if(empty($_GET['p'])) Throw new Exception(get_class() . " error: DoActionViewPage with empty key.");

		$this->key = $_GET['p'];
		return $this->GetPageAsForm();
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Create a new empty page
	//
	protected function DoActionNewPage() {
		if(empty($_GET['p'])) Throw new Exception(get_class() . " error: DoActionViewPage with empty key.");

		$this->SaveContent("<h1>New page</h1>\n<p>Edit this text to create your custom page.</p>", $_GET['p']);
		return $this->GetPage();
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Save page
	//
	protected function DoActionSavePage() {
		if(empty($_GET['p'])) Throw new Exception(get_class() . " error: DoActionSavePage with empty key.");

		$this->SaveContent($_POST['content'], $_GET['p']);
		CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"The page was saved.", "output-type"=>"success"));
	}
	

}