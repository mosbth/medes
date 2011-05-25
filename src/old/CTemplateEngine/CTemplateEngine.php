<?php
/**
 * Template engine, stores a collection of views and then renders them all.
 * @package MedesCore
 */
class CTemplateEngine {

  /**
   * Collection of views, will be rendered to the selected theme.
   * @var array of CView objects
   */
	protected $views;

  /**
   * Regions on the theme.
   * @var array of strings identifying the regions.
   */
	protected $region;

	
	/**
	 * Constructor
	 */
	public function __construct($cfg = null) {
		$this->views = array();
		$this->regions = $cfg['regions'];
	}
	
	
	/**
	 * Add view
	 */
	public function AddView($view, $prio=0, $target=null) {
		$this->views[] = array('view'=>$view, 'prio'=>$prio, 'target'=>$target);
	}
	
	
	/**
	 * Render the views
	 */
	public function Render() {
		global $pp;
	
		// Sort views
		function cmp($a, $b) {
			if($a['prio'] == $b['prio']) {
				return 0;
			}
			return $a['prio'] > $b['prio'] ? 1 : -1;
		}
		usort($this->views, 'cmp');
		
		// All template files
		$tpl = array(
			'page.tpl.php',
		);

		// Render each view
		foreach($tpl as $file) {
			$tplFile = $pp->cfg['config-db']['theme']['pathOnDisk'] . "/$file";
			if(is_file($tplFile)) {
				include $tplFile;
			} else {
	  		throw new Exception(t('#class error: Template file does not exist. File = @file', array('#class'=>get_class(), '@file'=>$tplFile)));			
			}
		}

		/*
		// Render each view
		foreach($this->views as $view) {
			$view['view']->Render();
		}
		*/
		// 
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Create code for correct doctype
	// 
	public function GetHTMLDocType() {
		switch($this->pageDocType) {
			case 'xhtml-strict':
				$xml = "<?xml version='1.0' encoding='{$this->pageCharset}' ?>";
				$html = <<<EOD
{$xml}
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$this->pageLang}" lang="{$this->pageLang}">
EOD;
				break;
			
			case 'html5':
			default:
				$html = <<<EOD
<!DOCTYPE html>
<html lang="{$this->pageLang}">
EOD;
				break;			
		}

		return $html;
	}


	// ------------------------------------------------------------------------------------
	//
	// Create html to include stylesheets based on theme choosen in config
	// 
	public function GetHTMLForStyle() {
		
		$pathToTheme = $this->PrependWithSiteUrl("medes/style/{$this->config['styletheme']['name']}");
		$stylesheet = isset($this->config['styletheme']['stylesheet']) ? "{$pathToTheme}/{$this->config['styletheme']['stylesheet']}" : "style/core/screen_compatibility.css";
		$print = isset($this->config['styletheme']['print']) ? "<link rel='stylesheet' media='print' type='text/css' href='{$pathToTheme}/{$this->config['styletheme']['print']}'/>\n" : "";
 		$ie = isset($this->config['styletheme']['ie']) ? "<!--[if IE]><link rel='stylesheet' media='screen, projection' type='text/css' href='{$pathToTheme}/{$this->config['styletheme']['ie']}'><![endif]-->\n" : "";
		$style = isset($this->pageStyle) ? "<style type='text/css'>{$this->pageStyle}</style>\n" : "";
		$favicon = empty($this->pageFaviconLink) ? null : $this->PrependWithSiteUrl($this->pageFaviconLink);
		$favicon = is_null($favicon) ? '' : "<link rel='shortcut icon' type='{$this->pageFaviconType}' href='{$favicon}'/>\n";

		$stylelinks='';
		foreach($this->pageStyleLinks as $val) {
			$media = isset($val['media']) ? "media='{$val['media']}'" : "media='all'";
			$type = isset($val['type']) ? "type='{$val['type']}'" : "type='text/css'";
			$href = "href='" . $this->PrependWithSiteUrl($val['href']) . "'";
			$stylelinks .= "<link rel='stylesheet' {$media} {$type} {$href}/>\n";
		}
		
		$html = <<<EOD
<link rel="stylesheet" media="all" type="text/css" href="{$stylesheet}"/>
{$print}
{$ie}
{$stylelinks}
{$style}
{$favicon}

EOD;

		return $html;
	}


	// ------------------------------------------------------------------------------------
	//
	// Get html for script
	//
	public function GetHTMLForScript() {
		$scriptlinks='';
		foreach($this->pageScriptLinks as $val) {
			$type = isset($val['type']) ? "type='{$val['type']}'" : "type='text/javascript'";
			$src = "src='" . $this->PrependWithSiteUrl($val['src']) . "'";
			$scriptlinks .= "<script {$type} {$src}></script>\n";
		}
		
		$script = isset($this->config['tracker']) ? "\n{$this->config['tracker']}\n" : "";
		$script .= isset($this->pageScript) ? "<script type='text/javascript'>\n{$this->pageStyle}\n</script>\n" : "";

		// Google analytics tracker code
		
		$html = <<<EOD
{$scriptlinks}
{$script}

EOD;

		return $html;		
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for related sites menu
	//
	public function GetHTMLForRelatedSitesMenu() {
		// treat all relative links as relative to sitelink, therefore prepend sitelink
		$nav = $this->config['navigation']['relatedsites']['nav'];
		return CNavigation::GenerateMenu($nav, false, '#mds-nav-relatedsites');		
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for login/logout/profile menu
	//
	public function GetHTMLForLoginMenu() {
		$nav = array(
			"login"=>array("text"=>"login", "url"=>$this->PrependWithSiteUrl("medes/page/ucp.php?p=login"), "title"=>"Login"),
			"settings"=>array("text"=>"settings", "url"=>$this->PrependWithSiteUrl("medes/page/ucp.php"), "title"=>"Change your settings"),
			"acp"=>array("text"=>"acp", "url"=>$this->PrependWithSiteUrl("medes/page/acp.php"), "title"=>"Admin Control Panel"),
			"logout"=>array("text"=>"logout", "url"=>$this->PrependWithSiteUrl("medes/page/ucp.php?p=dologout"), "title"=>"Logout"),
		);

		if($this->uc->IsAuthenticated()) {
			unset($nav['login']);
			$nav['settings']['text'] = $this->uc->GetAccountName();
			if(!$this->uc->IsAdministrator()) {
				unset($nav['acp']);			
			}
		} else {
			unset($nav['settings']);
			unset($nav['acp']);
			unset($nav['logout']);			
		}

		return CNavigation::GenerateMenu($nav, false, '#mds-nav-login');		
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for header 
	//
	public function GetHTMLForMeta() {
		$meta = "<meta charset='{$this->pageCharset}'/>\n";
		$meta .= is_null($this->pageKeywords) ? '' : "<meta name='keywords' content='{$this->pageKeywords}'/>\n";
		$meta .= is_null($this->pageDescription) ? '' : "<meta name='description' content='{$this->pageDescription}'/>\n";
		$meta .= is_null($this->pageAuthor) ? '' : "<meta name='author' content='{$this->pageAuthor}'/>\n";
		$meta .= is_null($this->pageCopyright) ? '' : "<meta name='copyright' content='{$this->pageCopyright}'/>\n";
		return $meta;
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for navbar 
	//
	public function GetHTMLForMainMenu() {
		//self::$menu[$p]['active'] = 'active';
		$cur = self::GetUrlToCurrentPage();
		$nav = $this->config['navigation']['navbar']['nav'];
		foreach($nav as $key => $val) {
			if(!(strstr($nav[$key]['url'], '://') || $nav[$key]['url'][0] == '/')) {
				$nav[$key]['url'] = $this->PrependWithSiteUrl($nav[$key]['url']);
			}
			if(strpos($cur, $nav[$key]['url'])) {
				$nav[$key]['active'] = "active";
			}
		}		
		return CNavigation::GenerateMenu($nav, false, 'mds-nav-mainmenu');		
  }


	// ------------------------------------------------------------------------------------
	//
	// Get html for debug menu, usually used during development 
	//
	public function GetHTMLForDeveloperMenu() {
		$url = $this->GetUrlToCurrentPage();
		$nav1 = array(
			"phpmedes"	=>array("text"=>"phpmedes:", "class"=>"strong nolink"),			
			"site"	=>array("text"=>"phpmedes.org", "url"=>"http://phpmedes.org/", "title"=>"home of phpmedes project"),			
		);

		$nav2 = array(
			"tools"					=>array("text"=>"Tools:", "class"=>"strong nolink"),			
			"html5"					=>array("text"=>"html5", "url"=>"http://validator.w3.org/check/referer", "title"=>"html5 validator"),			
			"css3"					=>array("text"=>"css3", "url"=>"http://jigsaw.w3.org/css-validator/check/referer?profile=css3", "title"=>"css3 validator"),			
			"unicorn"				=>array("text"=>"unicorn", "url"=>"http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance", "title"=>"unicorn html and css validator"),			
			"cheatsheet"		=>array("text"=>"cheatsheet", "url"=>"http://www.w3.org/2009/cheatsheet/", "title"=>"html cheatsheet, lookup html-tags"),			
			"link-checker"	=>array("text"=>"link checker", "url"=>"http://validator.w3.org/checklink?uri=" . $url, "title"=>"css3 validator"),			
			"i18n-checker"	=>array("text"=>"i18n checker", "url"=>"http://qa-dev.w3.org/i18n-checker/index?async=false&amp;docAddr=" . $url, "title"=>"css3 validator"),			
			"check-header"	=>array("text"=>"check http-header", "url"=>"http://jigsaw.w3.org/css-validator/check/referer?profile=css3", "title"=>"css3 validator"),			
			"browsers"			=>array("text"=>"browsers", "url"=>"http://browsershots.org/{$url}", "title"=>"check browser compatibility"),	
			"colors"				=>array("text"=>"colors", "url"=>"http://www.workwithcolor.com/hsl-color-schemer-01.htm", "title"=>"color chooser"),	
		);

		$nav3 = array(
			"manuals"	=>array("text"=>"Manuals:", "class"=>"strong nolink"),			
			"html5"		=>array("text"=>"html5", "url"=>"http://dev.w3.org/html5/spec/spec.html", "title"=>"html5 specification"),			
			"css2"		=>array("text"=>"css2", "url"=>"http://www.w3.org/TR/CSS2/", "title"=>"css2 specification"),			
			"css3"		=>array("text"=>"css3", "url"=>"http://www.w3.org/Style/CSS/current-work#CSS3", "title"=>"css3 specification"),			
			"php"			=>array("text"=>"php", "url"=>"http://php.net/manual/en/index.php", "title"=>"php manual"),			
			"sqlite"	=>array("text"=>"sqlite", "url"=>"http://www.sqlite.org/lang.html", "title"=>"sqlite manual"),			
			"blueprint"	=>array("text"=>"blueprint", "url"=>"https://github.com/joshuaclayton/blueprint-css/wiki/Tutorials", "title"=>"blueprint tutorials on github"),			
		);

		$item1 = CNavigation::GenerateMenu($nav1, false);
		$item2 = CNavigation::GenerateMenu($nav2, false);
		$item3 = CNavigation::GenerateMenu($nav3, false);
		$time = round(microtime(true) - self::$timePageGeneration, 5);
		$numQueries = CDatabaseController::$numQueries;

		$reload= "";
		if(isset($_SESSION['timer'])) {
			$reload = "Page processed and redirected in {$_SESSION['timer']['time']} seconds with {$_SESSION['timer']['numQueries']} database queries.<br/>";
			unset($_SESSION['timer']);
		}

		$html = <<<EOD
<p class="clear"><em>{$reload}Page generated in {$time} seconds. There were {$numQueries} database queries.</em></p>
{$item1}{$item2}{$item3}
EOD;

		return $html;
  }


	// ------------------------------------------------------------------------------------
	//
	// Print the complete html-page 
	// $aContent: the html-code for the main content of the page
	// $aSidebar1: html-code for sidebar1 of the page, if null then do not use sidebar1
	// $aSidebar2: html-code for sidebar1 of the page, if null then do not use sidebar2
	//
	public function PrintHTMLPage($aContent=null, $aSidebar1=null, $aSidebar2=null) {
		if(!is_null($aContent)) $this->pageContent = $aContent;
		if(!is_null($aSidebar1)) $this->pageSidebar1 = $aSidebar1;
		if(!is_null($aSidebar2)) $this->pageSidebar2 = $aSidebar2;		
		
		if($this->pageSidebar1 && $this->pageSidebar2) {
			$this->classContent="span-16 border";
			$this->classSidebar1="span-4 border";
			$this->classSidebar2="span-4 last";			
		} else if($this->pageSidebar1) {
			$this->classContent="span-19 last";
			$this->classSidebar1="span-4 colborder";
		} else if($this->pageSidebar2) {
			$this->classContent="span-18 colborder";
			$this->classSidebar2="span-5 last";
		}

		$pp = &$this;
		
		if(!is_null($this->pageContentType)) {
			header("Content-Type: {$this->pageContentType}; charset={$this->pageCharset}");
		}

		ob_start();
		echo eval("?>" . $this->config['htmlparts-htmlhead']);

		echo is_null($this->pageTop) ? eval("?>" . $this->config['htmlparts-pagetop']) : $this->pageTop;
		echo is_null($this->pageHeader) ? eval("?>" . $this->config['htmlparts-pageheader']) : $this->pageHeader;		

		echo eval("?>" . $this->config['htmlparts-pagecontent']);
	
		echo is_null($this->pageFooter) ? eval("?>" . $this->config['htmlparts-pagefooter']) : $this->pageFooter;
		echo is_null($this->pageBottom) ? eval("?>" . $this->config['htmlparts-pagebottom']) : $this->pageBottom;
		echo "</body>\n</html>\n";
		ob_end_flush();
  }






} // End of class
