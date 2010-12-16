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
	public function Save($aContent, $aKey=null) {
		if(isset($aKey)) {
			$this->key = $aKey;
		}
	
		if(empty($this->key)) {
			Throw new Exception(get_class() . " error: Saving content with empty key.");
		}
		
		$this->a->GetByKey($this->key);
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
		
		$this->a->GetByKey($this->key);
		return $this->a->GetContent();
	}


	// ------------------------------------------------------------------------------------
	//
	// Delete all pages. 
	//
	public function DeleteAll() {
		parent::DeleteAllByOwner(get_class());
	}


}