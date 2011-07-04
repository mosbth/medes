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
	 * The key to current article.  // OBSOLETE??
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
		

	/**#@+
	 * Utilities.
	 */
	public function ListAll() { return parent::ListByType(self::typeOfContent); }
	/**#@-*/


	/**
	 * Set the key of the page. 
	 */
	public function SetKey($aKey) {
		parent::SetKey($aKey);
		$this->key = $aKey;
	}


	/**
	 * Load the page. 
	 * @param int $id The id to load, or use current id if already set.
	 * @returns boolean if success of not.
	 */
	public function LoadById($id) {
		$r = parent::Load($id);
		$this->key = parent::GetKey();
		return $r;
	}


	/**
	 * Load the page. 
	 * @param string $key The key of the page to load.
	 * @returns boolean if success of not.
	 */
	public function LoadByKey($key) {
		$r = parent::LoadByKey($key);
		$this->key = parent::GetKey();
		return $r;
	}


	/**
	 * Does a page exists with this key?
	 */
	public function Exists($aKey=null) {
		if(empty($this->key) && empty($aKey)) throw new Exception(t('Can not do action with empty key.'));
		$key = isset($aKey) ? $aKey : $this->key; 
		return $this->ExistsByKey($key);
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
/*	public function GetContent($aKey=null) {
		if(isset($aKey)) $this->key = $aKey;
		if(empty($this->key)) throw new Exception(t('Getting content with empty key.'));
		
		$this->a->LoadByKey($this->key);
		return $this->a->GetContent();
	}
*/

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