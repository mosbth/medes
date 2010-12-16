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

class CContentPage extends CContent {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $key;
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
		if(isset($aKey)) {
			$this->key = $aKey;
		}
	
		if(empty($this->key)) {
			Throw new Exception(get_class() . " error: Saving content with empty key.");
		}
		
		$this->a->ClearCurrent();
		$this->a->SetKey($this->key);
		$this->a->SetType(self::TYPE);
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
		$uc = CUserController::GetInstance();
		$isAuthenticated = $uc->IsAuthenticated();
		$modified = $this->a->GetModified();
		$created = $this->a->GetCreated();
		$deleted = $this->a->GetDeleted();
		
		$html = "<p class='{$class}'>";
		$html .= $isAuthenticated ? "<a href='?editpage'>Edit this page</a>. ":'';
		$html .= $modified ? "Last modified {$modified}. " : "Created {$created}. ";
		$html .= " Owner is " . $this->a->GetOwner() . ".";
		$restorePage = $isAuthenticated ? "<a href='?restorepage'>Restore this page</a>":'';
		$deletePage = $isAuthenticated ? "<a href='?deletepage'>Delete this page</a>.":'';
		$html .= $deleted ? "Page was deleted {$deleted} ({$restorePage}). " : $deletePage;
		$html .= $isAuthenticated ? "<a href='?newpage'>Create new page</a>. ":'';
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


}