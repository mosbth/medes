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


	/**
	 * Get content and preprocess according to its filter, if any. Useful when adding content
	 * directly to a view.
	 * @param string $aKey The key of the content
	 * @return array with the key to the view and with the preprocessed content.
	 */
	public function GetFilteredContent() {
    $content = $this->GetContent();
    $filter  = $this->GetFilter();
    $type = 'html';
    $allowed = '<i><b><strong><em><p><img><a><h1><h2><h3><h4><h5><h6><ul><li><ol>';
    switch($filter) {
      case 'php':   	$content = bbcode2html($content); $type = 'php'; break;
    	case 'html': 		$content = bbcode2html($content); break;
    	case 'bbcode': 	$content = nl2br(bbcode2html(strip_tags($content, $allowed))); break;
    	case 'fhtml': 	$content = nl2br(strip_tags($content, $allowed)); break;
      case 'text':  	$content = nl2br(sanitizeHTML($content)); break;
    }
		return array($type=>$content);
	}


	/**#@+
	 * Utilities.
	 */
	public function Load($id = null) { return $this->a->Load($id); }
	public function LoadByKey($key) { return $this->a->LoadByKey($key); }
	public function Save() { return $this->a->Save(); }
	public function Delete() { return $this->a->Delete(); }
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
	public function GetFilter() { return $this->a->GetFilter(); }
	public function SetFilter($val) { return $this->a->SetFilter($val); }
	public function GetTemplate() { return $this->a->GetTemplate(); }
	public function SetTemplate($val) { return $this->a->SetTemplate($val); }
	/**#@-*/

}