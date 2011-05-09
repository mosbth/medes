<?php
// ===========================================================================================
//
// File: CContent.php
//
// Description: Abstract baseclass for content. Use any subclass to work with content
// that uses the CArticle structure for storing and editing content.
//
// Author: Mikael Roos
//
// History:
// 2010-12-14: Created
//

abstract class CContent {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	public $a;
	
	
	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	protected function __construct() {
		$this->a = new CArticle();
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	protected function __destruct() {;}
	

	// ------------------------------------------------------------------------------------
	//
	// Delete all articles by owner. 
	//
	public function DeleteAllByOwner($aOwner) {
		$this->a->DeleteAllByOwner($aOwner);
	}


}