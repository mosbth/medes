<?php
/**
 * Creating pages with key and content stored in database.
 *
 * A simple way to create pages using a online editor.
 * 
 * @package MedesCore
 */
class CContentPage extends CContent {

	/**#@+
	 * @access private
   */

	/**
	 * The key to current article.
	 * @var string
   */	
	private $key;
	
	/**
	 * Type of content, to identfy among other content.
	 * @var string
   */	
	const typeOfContent = "CContentPage";

	/**
	 * Aids in protecting access.
	 * @var CInterceptionFilter
   */	
	private $if; 
	/**#@-*/

	
	
	/**
	 * Constructor
	 */
	public function __construct($aKey="") {
		parent::__construct();
		$this->key = $aKey;
		$this->if = CInterceptionFilter::GetInstance();
	}
	
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		parent::__destruct();
	}
		

	/**
	 * Set the key of the page. 
	 */
	public function SetKey($aKey) {
		$this->key = $aKey;
	}


	/**
	 * Save page content. 
	 */
	public function SaveContent($aContent, $aKey=null) {
		if(!is_null($aKey)) $this->key = $aKey;
		if(empty($this->key)) throw new Exception(t('Saving content with empty key.'));
		
		$this->a->ClearCurrent();
		$this->a->SetKey($this->key);
		$this->a->SetType(self::typeOfContent);
		$this->a->SetTitle($this->key);
		$this->a->SetContent($aContent);
		$this->a->SetDraftTitle(null);
		$this->a->SetDraftContent(null);
		$this->a->Save();
		$this->a->UnsetDraft();
	}


	/**
	 * Save draft page content. 
	 */
	public function SaveDraftContent($aContent, $aKey=null) {
		if(!is_null($aKey)) $this->key = $aKey;
		if(empty($this->key)) throw new Exception(t('Saving content with empty key.'));
		
		$this->a->ClearCurrent();
		$this->a->SetKey($this->key);
		$this->a->SetType(self::typeOfContent);
		$this->a->SetDraftTitle($this->key);
		$this->a->SetDraftContent($aContent);
		$this->a->Save();
	}


	/**
	 * Get page content. 
	 */
	public function GetContent($aKey=null) {
		if(isset($aKey)) $this->key = $aKey;
		if(empty($this->key)) throw new Exception(t('Getting content with empty key.'));
		
		$this->a->LoadByKey($this->key);
		return $this->a->GetContent();
	}


	/**
	 * Get draft page content, if it exists, otherwise get page content. 
	 */
	public function GetDraftContent($aKey=null) {
		if(isset($aKey)) $this->key = $aKey;
		if(empty($this->key)) throw new Exception(t('Getting draft content with empty key.')); 
		
		$this->a->LoadByKey($this->key);
		
		$draft = $this->a->GetDraftContent();
		if(is_null($draft)) {
			return $this->a->GetContent();
		}
		return $draft;
	}


	/**
	 * Rename page key. 
	 */
	public function Rename($newKey) {
		if(empty($this->key) || empty($newKey))	throw new Exception(t('Doing action with empty key.'));
		if(($res = $this->a->RenameKey($this->key, $newKey)) == true) {
			$this->key = $newKey;
		}
		return $res;
	}


	/**
	 * Delete all pages. 
	 */
	public function DeleteAll() {
		parent::DeleteAllByOwner(get_class());
	}


	// ====================================================================================
	//
	//	Code below relates to views
	//

/*
	// ------------------------------------------------------------------------------------
	//
	// Gather all language-strings behind one method. 
	// Store all strings in self::$lang.
	//
	public static function InitLanguage($language=null) {
		if(is_null(self::$lang)) {
			self::$lang = array( 
				'CALLING_METHOD_WITH_EMPTY_KEY'=>'Calling method (%s) with empty key.',
				'PAGE_DELETED_TO_WASTEBASKET'=>'Page was deleted  and moved to wastebasket.',
				'PAGE_RESTORED_FROM_WASTEBASKET'=>'Page was restored from wastebasket.',
			);
		}
	}
*/


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
	// Get view with form to rename page. 
	//
	public function GetViewRenamePageForm() {
		$pp = CPrinceOfPersia::GetInstance();
		$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
		$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
		$this->GetContent();
		$statusBar = $this->GetStatusBar();
		$details = $this->GetViewSideMenu();
				
		$page = <<<EOD
<div class=span-18>
	{$statusBar}
	<form action='?p={$this->key}&amp;e&amp;a=renamePage' method=post>
		<fieldset>
			<legend>Rename page: {$this->key}</legend>		
			<p>
				<label for=input1>New name:</label><br>
				<input id=input1 type=text class=text name=newName value="{$this->key}">
			</p>
			<p class=left>
				<input type=submit name=doRenamePage value='Rename' {$disabled}>
				<input type=reset value='Reset'>
			</p>
			<p class=right><output class="span-5 {$remember['output-type']}">{$remember['output']}</output></p>	
		</fieldset>
	</form>
</div>
<div class="span-6 last quiet">
	{$details}
</div>

EOD;
		return $page;
	}


/*
	// ------------------------------------------------------------------------------------
	//
	// Get view with form to create new page by entering page name. 
	//
	public function GetViewCreatePageForm() {
		$pp = CPrinceOfPersia::GetInstance();
		$remember = $pp->GetAndClearRememberFromSession(array('output'=>'', 'output-type'=>''));
		$disabled = $pp->uc->IsAdministrator() ? "" : "disabled";
		$this->GetContent();
		$statusBar = $this->GetStatusBar();
		$details = $this->GetViewSideMenu();
				
		$page = <<<EOD
<div class=span-18>
	{$statusBar}
	<form action='?p={$this->key}&amp;e&amp;a=createPage' method=post>
		<fieldset>
			<legend>Create page: {$this->key}</legend>		
			<p>
				<label for=input1>Name of page:</label><br>
				<input id=input1 type=text class=text name=newName value="{$this->key}">
			</p>
			<p class=left>
				<input type=submit name=doCreatePage value='Create' {$disabled}>
				<input type=reset value='Reset'>
			</p>
			<p class=right><output class="span-5 {$remember['output-type']}">{$remember['output']}</output></p>	
		</fieldset>
	</form>
</div>
<div class="span-6 last quiet">
	{$details}
</div>

EOD;
		return $page;
	}
*/


	// ====================================================================================
	//
	//	Code below relates to the interface IActionHandler
	//

	
/*
	// ------------------------------------------------------------------------------------
	//
	// Show view to create a new empty page
	//
	protected function DoActionViewNewPage() {
		throw new Exception(__METHOD__ . " error: not yet implemented.");
	}
*/

/*
	// ------------------------------------------------------------------------------------
	//
	// Display the view to create a new empty page
	//
	protected function DoActionViewCreatePage() {
		//if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));

		//$this->SaveDraftContent("<h1>New page</h1>\n<p>Edit this text to create your custom page.</p>", $_GET['p']);
		return $this->GetViewCreatePageForm();
	}
*/


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
	// Destroy draft page
	//
	protected function DoActionDestroyDraftPage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));

		$this->a->LoadByKey($_GET['p']);
		$this->a->UnsetDraft();
		$url = CPrinceOfPersia::ModifyQueryStringOfCurrentUrl(array("a"=>null));
		CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Draft was destroyed.", "output-type"=>"success"), $url);
	}


	// ------------------------------------------------------------------------------------
	//
	// Save and Publish page
	//
	protected function DoActionSaveAndPublishPage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));

		$this->SaveContent($_POST['content'], $_GET['p']);
		$this->a->Publish();
		CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Page was saved and published.", "output-type"=>"success"));
	}


	// ------------------------------------------------------------------------------------
	//
	// Publish page
	//
	protected function DoActionPublishPage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));

		$this->a->LoadByKey($_GET['p']);
		$this->a->Publish();
		$url = CPrinceOfPersia::ModifyQueryStringOfCurrentUrl(array("a"=>null));
		CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Page was saved and published.", "output-type"=>"success"), $url);
	}


	// ------------------------------------------------------------------------------------
	//
	// Unpublish page
	//
	protected function DoActionUnpublishPage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));

		$this->a->LoadByKey($_GET['p']);
		$this->a->Unpublish();
		$url = CPrinceOfPersia::ModifyQueryStringOfCurrentUrl(array("a"=>null));
		CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Page was unpublished.", "output-type"=>"success"), $url);
	}


	// ------------------------------------------------------------------------------------
	//
	// Show view to rename page
	//
	protected function DoActionViewRenamePage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));
		
		$this->key = $_GET['p'];
		return $this->GetViewRenamePageForm();
	}


	// ------------------------------------------------------------------------------------
	//
	// Rename page
	//
	protected function DoActionRenamePage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));
		
		$this->key = $_GET['p'];
		$newName = $_POST['newName'];
		
		if($this->key == $newName) {
			CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Page was not renamed. New name was the same as the current name.", "output-type"=>"error"));	
		} else if(empty($newName)) {
			CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Page was not renamed. New name can not be empty.", "output-type"=>"error"));			
		} else if(($res = $this->Rename($_POST['newName'])) === true) {
			$url = CPrinceOfPersia::ModifyQueryStringOfCurrentUrl(array("p"=>"{$this->key}"));
			CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Page was renamed.", "output-type"=>"success"), $url);
		} else {
			CPrinceOfPersia::ReloadPageAndRemember(array("output"=>"Page was not renamed. {$res}.", "output-type"=>"error"));		
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Delete page
	//
	protected function DoActionDeletePage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));

		$this->a->LoadByKey($_GET['p']);
		$this->a->Delete();
		$url = CPrinceOfPersia::ModifyQueryStringOfCurrentUrl(array("a"=>null));
		CPrinceOfPersia::ReloadPageAndRemember(array("output"=>self::$lang['PAGE_DELETED_TO_WASTEBASKET'], "output-type"=>"success"), $url);
	}


	// ------------------------------------------------------------------------------------
	//
	// Restore a deleted page
	//
	protected function DoActionRestorePage() {
		if(empty($_GET['p'])) throw new Exception(sprintf(self::$lang['CALLING_METHOD_WITH_EMPTY_KEY'], __METHOD__));

		$this->a->LoadByKey($_GET['p']);
		$this->a->Restore();
		$url = CPrinceOfPersia::ModifyQueryStringOfCurrentUrl(array("a"=>null));
		CPrinceOfPersia::ReloadPageAndRemember(array("output"=>self::$lang['PAGE_RESTORED_FROM_WASTEBASKET'], "output-type"=>"success"), $url);
	}


	// ------------------------------------------------------------------------------------
	//
	// View list of pages
	//
	protected function DoActionViewPages() {
		throw new Exception(__METHOD__ . " error: not yet implemented.");
	}


}