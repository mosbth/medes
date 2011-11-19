<?php
/**
 * Configuration array for medes. 
 *
 * Edit it and use setup.php to populate the database with it.
 *
 * @package MedesCore
 */
$cfg = array(


/**
 * Internal stuff for medes.
 *
 * May be used for keeping track of what version is currently installed.
 *
 */
'medes' => array(
	'version' => MEDES_VERSION,		
),



/**
 * General settings.
 *
 */
'general'=> array(
  'clean_url' => false, //set to false if mod_rewrite is unavalable
),


/**
 * Define the controllers, their classname and enable/disable them.
 *
 * The array-key is matched against the url, for example: 
 * the url 'user/login' would instantiate the controller with the key "user", that is CCtrl4User
 * and call the method "login" in that class. This process is managed in:
 * $pp->FrontControllerRoute();
 * and this method is called in the frontcontroller phase from index.php.
 */
'controllers' => array(
  'index'     => array('enabled' => true,'class' => 'CCtrl4Home'),
  'error'     => array('enabled' => true,'class' => 'CCtrl4Error'),
  'home'      => array('enabled' => true,'class' => 'CCtrl4Home'),
  'user'      => array('enabled' => true,'class' => 'CCtrl4User'),
  'developer' => array('enabled' => true,'class' => 'CCtrl4Developer'),
  'themes'    => array('enabled' => true,'class' => 'CCtrl4Theme'),
  'cpage'     => array('enabled' => true,'class' => 'CCtrl4ContentPage'),
  'canurl'    => array('enabled' => true,'class' => 'CCtrl4CanonicalUrl'),
  'acp'       => array('enabled' => true,'class' => 'CCtrl4AdminControlPanel'),
),
// Define your own homepage
//'home' => array('title' => 'The Homepage', 'href' => 'home1'), 


/**
 * Define menus and menu items.
 *
 * The menus are called from the template page, for example page.tpl.php, using the method:
 * echo $pp->GetHTMLForMenu('relatedsites');
 * The class and method responsible for creating the actual menu is:
 * CNavigation::GenerateMenu();
 * 
 */
'menus' => array(
  'list-style' => false,
  
  // Here comes the menu called 'relatedsites'
  'relatedsites' => array(
    'enabled' => true,
    'id'      => 'mds-nav-relatedsites',
    'class'   => null,
    'items'   => array(
      array('text' => 'mikaelroos', 'href' => 'http://mikaelroos.se/',  'class' => '', 'title' => t('Just Me'),),
      array('text' => 'phpmedes',   'href' => 'http://phpmedes.org/',   'class' => '', 'title' => t('Homepage for PHPMedes'),),
      array('text' => 'dbwebb',     'href' => 'http://dbwebb.se/',      'class' => '', 'title' => t('Educational site for development with databases and webapplications'),),
    ),
  ),

  // Here is the login menu, it will change look depending on the user is authenticated or not
  'login' => array(
    'enabled'   => true,
    'id'        => 'mds-nav-login',
    'class'     => null,
    'callback'  => 'CPrinceofPersia::ModifyLoginMenu',
    'items'     => array(
      'login'     => array('text' => 'login',     'href' => 'user/login',     'class' => '', 'title' => t('Login as user'),),
      'settings'  => array('text' => 'settings',  'href' => 'user/settings',  'class' => '', 'title' => t('View/edit settings for this account and for this site'),),
      'acp'       => array('text' => 'acp',       'href' => 'acp',            'class' => '', 'title' => t('Admin Control Panel'),),
      'logout'    => array('text' => 'logout',    'href' => 'user/logout',    'class' => '', 'title' => t('Logout'),),
    ),
  ),
  
  // This is the main navigation menu
  'main' => array(
    'enabled'   => true,
    'id'        => 'mds-nav-main',
    'class'     => 'mds-nav-main',
    'callback'  => 'CPrinceofPersia::ModifyMenuDisplayCurrent',
    'items'     => array(
      array('text' => 'home',       'href' => 'home',       'class' => '', 'title' => t('Home'),),
      array('text' => 'developer',  'href' => 'developer',  'class' => '', 'title' => t('Aid for the developer'),),
      array('text' => 'themes',     'href' => 'themes',     'class' => '', 'title' => t('Aid for the themer'),),
      array('text' => 'pages',      'href' => 'cpage',      'class' => '', 'title' => t('pages'),),
      array('text' => 'canurls',    'href' => 'canurl',     'class' => '', 'title' => t('canurls'),),
    ),
  ),
),


/**
 * Define theme and enable site specific modifications.
 *
 * The templateengine is the last part that is called from index.php:
 * $pp->TemplateEngineRender();
 * It consists of settings, stylesheets, functions and template files which are all combined
 * when rendering the defined views to the resulting page with its regions.
 * 
 */
'theme'=> array(
  'name'      => 'Medes Core Theme',
  'regions'   => array('top-left', 'top-right', 'header', 'promoted', 'content', 'sidebar1', 'sidebar2', 'triptych1', 'triptych2', 'triptych3', 'footercol1', 'footercol2', 'footercol3', 'footercol4', 'footer'),
  'realpath'  => MEDES_INSTALL_PATH . '/theme/core',
  'url'       => 'theme/core', // Will prepend urlpath to favicon, logo and stylesheets
  'favicon'   => 'img/favicon.png',
  'logo'      => array('src'=>'img/logo_medes_330x70.png', 'alt'=>'Medes Logo', 'width'=>330, 'height'  => 70,),
  'stylesheets' => array(
    array('file'=>'style/screen.css','type'=>'text/css','media'=>'screen'),
    array('file'=>'style/ish_drupal.css','type'=>'text/css','media'=>'screen', 'enabled'=>false),
    array('file'=>'style/ish_wordpress.css','type'=>'text/css','media'=>'screen', 'enabled'=>false),
    array('file'=>'style/ish_stylish.css','type'=>'text/css','media'=>'screen', 'enabled'=>false),

    // enable to make site modifications by adding stylesheets in site-directory 
    //'site-mods' => array('file' => '../site/theme/style.css', 'media'=>'screen'),
  ),

  // enable to add site specific functions to theme 
  //'functions' => array(MEDES_SITE_PATH . '/theme/functions.php',),
  
  // Template files, where the actual content goes, the resulting page. Will add realpath to those
  // with relative paths, absolute paths will be untouched.
  'templates' => array(
    'default' => 'page.tpl.php',
    'page'    => 'page.tpl.php',
    'empty' 	=> 'empty.tpl.php',
    
    // enable to add site specific templates to extend current theme
    //'xxx'		=> MEDES_SITE_PATH . 'theme/xxx.tlp.php'
  ),
  
  // Add hardcoded values and extract them in your template page using echo $pp->GetHTMLMessage('footer');
  'messages' => array(
    'sitetitle' => '',
    'footer'    => '<p>This is the footer</p>',
    'copyright' => '<p>&copy; PHPMedes, free and opensource software.</p>',
  ),
  
  // Add a default page title when pages does not create their own. Get it through echo $pp->GetHTMLForPageTitle();
  'pagetitle' => array(
    'default' => 'No title set',		
    'prepend' => 'phpmedes: ',		
  ),

  // Add entries for the specific doctype
  'doctype' => array(
    'doctype'     => 'html5',		
    'contenttype' => 'text/html',
    'lang'        => 'en',
  ),

  // Add meta entries as strings and get them in the template with echo $pp->GetHTMLForMeta();
  'meta' => array(
    'language'    => 'en',		
    'keywords'    => null,		
    'description' => null,		
    'author'      => null,		
    'copyright'   => null,		
  ),

  'developer_tools' => true, // Should the developer tools be displayed?
),


/**
 * Add javascript.
 *
 * Its the template file that calls the methods for getting the HTML to display for including
 * javaScript external files:
 * echo $pp->GetHTMLForScript();
 * The tracker is pure Javascript, including the <javascript>-tags.
 * 
 */
'js'=> array(
  'external' => array(
    //'jquery'    => array('enabled'=>true, 'src'=>'https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'),
  ),
  'tracker' => null,
),


/**
 * End of configuration array.
 */
);
