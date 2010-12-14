<?php
// ===========================================================================================
//
// File: CRSSFeed.php
//
// Description: Provide a interface to create a RSS feed and store it on file.
//
// Author: Mikael Roos
//
// History:
// 2010-12-04: Created
//

class CRSSFeed {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
	// channel vars
	protected $channel_url;
	protected $channel_title;
	protected $channel_description;
	protected $channel_lang;
	protected $channel_copyright;
	protected $channel_date;
	protected $channel_creator;
	protected $channel_subject;   
	// image
	protected $image_url;
	// items
	protected $items = array();
	protected $nritems;


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {
		$this->nritems=0;
		$this->channel_url='';
		$this->channel_title='';
		$this->channel_description='';
		$this->channel_lang='';
		$this->channel_copyright='';
		$this->channel_date='';
		$this->channel_creator='';
		$this->channel_subject='';
		$this->image_url='';
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Output feed. 
	//
	public function Output() {

		$rdf = "";
		for($k=0; $k<$this->nritems; $k++) {
				$rdf .= '<rdf:li rdf:resource="'.$this->items[$k]['url'].'"/>'."\n"; 
		}

		$items = "";
		for($k=0; $k<$this->nritems; $k++) {
			$items .= <<<EOD
<item rdf:about="{$this->items[$k]['url']}">
<title>{$this->items[$k]['title']}</title>
<link>{$this->items[$k]['url']}</link>
<description>{$this->items[$k]['description']}</description>
<feedburner:origLink>{$this->items[$k]['url']}</feedburner:origLink>
</item>

EOD;
		};

		$output = <<<EOD
<?xml version="1.0" encoding="iso-8859-1"?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:syn="http://purl.org/rss/1.0/modules/syndication/" xmlns:admin="http://webns.net/mvcb/" xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0">
<channel rdf:about="'.$this->channel_url.'">
<title>{$this->channel_title}</title>
<link>{$this->channel_url}</link>
<description>{$this->channel_description}</description>
<dc:language>{$this->channel_lang}</dc:language>
<dc:rights>{$this->channel_copyright}</dc:rights>
<dc:date>{$this->channel_date}</dc:date>
<dc:creator>{$this->channel_creator}</dc:creator>
<dc:subject>{$this->channel_subject}</dc:subject>
<items>
<rdf:Seq>
{$rdf}
</rdf:Seq>
</items>
<image rdf:resource="{$this->image_url}"/>
</channel>
{$items}
</rdf:RDF>

EOD;		

  	return $output;
	}

	// ------------------------------------------------------------------------------------
	//
	// Set channel variables. 
	//
	public function SetChannel($url, $title, $description, $lang, $copyright, $creator, $subject) {
		$this->channel_url=$url;
		$this->channel_title=$title;
		$this->channel_description=$description;
		$this->channel_lang=$lang;
		$this->channel_copyright=$copyright;
		$this->channel_date=date("Y-m-d").'T'.date("H:i:s").'+01:00';
		$this->channel_creator=$creator;
		$this->channel_subject=$subject;
   }

	// ------------------------------------------------------------------------------------
	//
	// Set image 
	//
	public function SetImage($url) {
		$this->image_url=$url;  
	}

	// ------------------------------------------------------------------------------------
	//
	// Add item to feed. 
	//
	public function AddItem($url, $title, $description) {
		$this->items[$this->nritems]['url']=$url;
		$this->items[$this->nritems]['title']=$title;
		$this->items[$this->nritems]['description']=$description;
		$this->nritems++;
  }


}