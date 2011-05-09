<?php
// ===========================================================================================
//
// File: CRSSReader.php
//
// Description: Provide a interface to get RSS feed.
//
// Author: Mikael Roos
//
// History:
// 2010-12-04: Created
//

class CRSSReader {

	// ------------------------------------------------------------------------------------
	//
	// Protected internal variables
	//
/*
	protected static $menu = array(
		"home" => array("text"=>"/admin control panel/", "url"=>"acp.php", "title"=>"Administrate and configure the site and its addons", "class"=>"nav-h1"),

		"site" => array("text"=>"Configure site", "title"=>"Configure and define site related items", "class"=>"nav-h2 nolink"),
		"siteurl" => array("text"=>"site link", "url"=>"?p=siteurl", "title"=>"Set the main link to the site"),
		"meta" => array("text"=>"meta",  "url"=>"?p=meta", "title"=>"Set default meta tags to enhace search enginge visibility"),
		"tracker" => array("text"=>"tracker",  "url"=>"?p=tracker", "title"=>"Track site using Google Analytics"),
		"htmlparts" => array("text"=>"htmlparts", "url"=>"?p=htmlparts", "title"=>"Change htmlparts of site, including header and footer"),
		"navigation" => array("text"=>"navigation", "url"=>"?p=navigation", "title"=>"Define the site navigation menus, including your own navigational menus"),
		"stylesheet" => array("text"=>"stylesheet", "url"=>"?p=stylesheet", "title"=>"Set and edit the stylesheet"),
		"debug" => array("text"=>"debug", "url"=>"?p=debug", "title"=>"Print out debug information and current configuration"),

		"addons" => array("text"=>"Configure addons", "title"=>"Install, update and configure addons", "class"=>"nav-h2 nolink"),
		"fileupload" => array("text"=>"fileupload", "url"=>"?p=fileupload", "title"=>"Upload files and images"),

		"other" => array("text"=>"Other", "title"=>"Other things, to be removed?", "class"=>"nav-h2 nolink"),
		"changepwd" => array("text"=>"change password", "url"=>"?p=changepwd", "title"=>"Change the administrator password"),
//		"header" => array("text"=>"header", "url"=>"?p=header", "title"=>"Define the header and logo of the site"),
//		"footer" => array("text"=>"footer", "url"=>"?p=footer", "title"=>"Define the footer of the site"),
//		"relatedsites" => array("text"=>"related sites", "url"=>"?p=relatedsites", "title"=>"Use and define related sites"),
//		"navbar" => array("text"=>"navigation bar", "url"=>"?p=navbar", "title"=>"Define the navigation bar (main menu) of the site"),
	);

	protected static $pages = array(
		"home" => array("file"=>"home.php", "title"=>"Home of admin area"),
		"changepwd" => array("file"=>"changepwd.php", "title"=>"Admin area: change password"),
		"siteurl" => array("file"=>"siteurl.php", "title"=>"Admin area: set sitelink"),
		"meta" => array("file"=>"meta.php", "title"=>"Admin area: set meta information"),
		"tracker" => array("file"=>"tracker.php", "title"=>"Admin area: enable tracking using Google Analytics"),
		"htmlparts" => array("file"=>"htmlparts.php", "title"=>"Admin area: edit htmlparts of the site"),
//		"header" => array("file"=>"header.php", "title"=>"Admin area: define the header of the site"),
//		"footer" => array("file"=>"footer.php", "title"=>"Admin area: define the footer of the site"),
		"navigation" => array("file"=>"navigation.php", "title"=>"Admin area: define and set navigation menus"),
//		"relatedsites" => array("file"=>"relatedsites.php", "title"=>"Admin area: use and define related sites"),
//		"navbar" => array("file"=>"navbar.php", "title"=>"Admin area: set navigation bar, the main menu"),
		"stylesheet" => array("file"=>"stylesheet.php", "title"=>"Admin area: set and edit the stylesheet"),
		"fileupload" => array("file"=>"fileupload.php", "title"=>"Admin area: upload files and images"),
		"debug" => array("file"=>"debug.php", "title"=>"Admin area: print out debug and config information"),
	);
*/


	// ------------------------------------------------------------------------------------
	//
	// Public internal variables
	//
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {;}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Get feed. 
	//
	public static function GetContent($aFeedUrl, $maxItems=10) {

		$pp = CPrinceOfPersia::GetInstance();

		// import the code
		include_once('simplepie/simplepie.class.php');
		include_once('simplepie/idn/idna_convert.class.php');
		$feed = new SimplePie();
		
		// check or create cachefile
		$cache = $pp->medesPath . "/data/" . get_called_class() . ".cache";
		if(!is_dir($cache)) {
			mkdir($cache);
		}
		$feed->enable_cache();		
		$feed->set_cache_location($cache);
		
		// check the feed
		$feed->set_feed_url($aFeedUrl);
		$success = $feed->init();
		if(!$success) {
			echo "<p>Failed initiating feed.";
		}
		$feed->handle_content_type();

		if ($feed->error())	{
			echo '<p>Feed Error' . htmlspecialchars($feed->error()) . "</p>";
		}

		// present the content
		$feedHtml = "";
		if ($success) {
			$titleLink = $feed->get_title();
			$title = (empty($titleLink) ? "" : "<a href='$titleLink'>") . $feed->get_title() . (empty($titleLink) ? "" : "</a>");
			$description = $feed->get_description();
			$feedHtml = <<<EOD
<h4>{$title}</h4>
<p>{$description}</p>
EOD;
			
			$i=0;
			foreach($feed->get_items() as $item) {
			
				$permalink = $item->get_permalink();
				$title = (empty($permalink) ? "" : "<a href='$permalink'>") . $item->get_title() . (empty($permalink) ? "" : "</a>");
				$date = $item->get_date('j M Y, g:i a');
				$content = substr(strip_tags($item->get_content()), 0, 80) . "...";
	
				$enclosureHtml = "";
				if ($enclosure = $item->get_enclosure(0)) {
					$enclosureHtml .= '<p>' . $enclosure->embed(array(
									'audio' => './for_the_demo/place_audio.png',
									'video' => './for_the_demo/place_video.png',
									'mediaplayer' => './for_the_demo/mediaplayer.swf',
									'altclass' => 'download'
								)) . '</p>';
	
					if ($enclosure->get_link() && $enclosure->get_type()) {
						$enclosureHtml .= '<p class="footnote" align="center">(' . $enclosure->get_type();
						if ($enclosure->get_size()) {
							$enclosureHtml .= '; ' . $enclosure->get_size() . ' MB';
						}
						$enclosureHtml .= ')</p>';
					}
					if ($enclosure->get_thumbnail()) {
						$enclosureHtml .= '<div><img src="' . $enclosure->get_thumbnail() . '" alt="" /></div>';
					}
					$enclosureHtml .= '</div>';
				}

				$feedHtml .= <<<EOD
<p>
{$title}<br>
{$date}<br>
{$content}
</p>		
EOD;

				if(++$i >= $maxItems) {
					break;
				}
			}
		} else {
			echo "<p>Failed initiating feed.";
		}
		return $feedHtml;
	}
	

/*	
	// ------------------------------------------------------------------------------------
	//
	// Frontcontroller. Redirect to choosen page and return the resulting html. 
	//
	public static function DoIt() {
		
		$pp = CPrinceOfPersia::GetInstance();
		
		// Check and get the current page referer
		$p = isset($_GET['p']) && array_key_exists($_GET['p'], self::$pages) ? $_GET['p'] : 'home'; 		
		
		// Set the current menu choice to active
		self::$menu[$p]['active'] = 'active';

		// Prepare the html for the page
		$pp->pageTitle = self::$pages[$p]['title'];
		$sidemenu = CNavigation::GenerateMenu(self::$menu, false, 'sidemenu');
		
		// Process the actual page and fill in $page
		require(dirname(__FILE__) . "/" . self::$pages[$p]['file']);

		// Create the resulting page
		$html = <<<EOD
<article class="span-18">
	{$page}
</article>	
<aside class="span-6 last">
	{$sidemenu}
</aside>
EOD;

		return $html;
	}
*/

}