<?php
/**
 * Abstract baseclass for content. 
 *
 * Use any subclass to work with content that uses the CArticle structure for storing and 
 * editing content.
 * 
 * @package MedesCore
 */
abstract class CContent {

	/**#@+
	 * @access public
   */

	/**
	 * Reference to the article.
	 * @var CArticle
   */	
	protected $a;
	/**#@-*/

	
	/** 
	 * Constructor
	 */
	protected function __construct() {
		$this->a = new CArticle();
	}
	
	
	/**
	 * Destructor
	 */
	protected function __destruct() {;}
	

	/**
	 * Delete all articles by owner. 
	 */
	public function DeleteAllByOwner($aOwner) {
		$this->a->DeleteAllByOwner($aOwner);
	}


	/**
	 * Check if content with key exists.
	 * @param string $aKey The key of the content
	 * @return mixed Returns the value of the id, either a value or null if it does not exists. 
	 */
	public function ExistsByKey($aKey) {
		$b = new CArticle();
		$b->LoadByKey($aKey);
		return $b->GetId();
	}


	/**#@+
	 * Utilities.
	 */
	public function Load($id = null) { return $this->a->Load($id); }
	public function LoadByKey($key) { return $this->a->LoadByKey($key); }
	public function Save() { return $this->a->Save(); }
	public function ListByType($type) { return $this->a->ListByType($type); }
	/**#@-*/

	/**#@+
	 * Setters and Getters.
	 */
	public function GetId() { return $this->a->GetId(); }
	public function GetKey() { return $this->a->GetKey(); }
	public function SetKey($val) { return $this->a->SetKey($val); }
	public function GetTitle() { return $this->a->GetTitle(); }
	public function SetTitle($val) { return $this->a->SetTitle($val); }
	public function GetCanonicalUrl() { return $this->a->GetCanonicalUrl(); }
	public function SetCanonicalUrl($val) { return $this->a->SetCanonicalUrl($val); }
	public function GetContent() { return $this->a->GetContent(); }
	public function SetContent($val) { return $this->a->SetContent($val); }
	/**#@-*/

}