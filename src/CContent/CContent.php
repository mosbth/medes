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
	public $a;
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


}