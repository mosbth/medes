<?php
/**
 * Setting and install Medes, use to update configuration when needed.
 *
 * Point your browser to this file to install or update the configuration of this medes site.
 *
 * @package MedesCore
 */

// --------------------------------- Checking preconditions ---------------------------------------

// Start a named session
//session_name('medes_install');
//session_start();

// Change site path if needed, default is in MEDES_INSTALL_PATH/site
//define('MEDES_SITE_PATH', SOMEWHERE . 'site');
if(!defined('MEDES_SITE_PATH')) {
  define('MEDES_SITE_PATH', __DIR__ . 'site');
}

// Check if config.php exists
echo "<h1>PHPMedes setup</h1><p>Checking site/config.php...";

if(is_readable(MEDES_SITE_PATH . '/config.php')) {
  echo "file exists. OK.";
} else {
  echo "file does not exists. Copy site/config-sample.php to site/config.php and edit it.</p>";
  die("<p>Fix the error and then reload this page and try again.</p>");
}


//
// Start it up
// Use Medes bootstrap to gain access to medes environment
// disgard the rest
//
define('MEDES_INSTANTIATE_PASS', true);
define('MEDES_FRONTCONTROLLER_PASS', true);
define('MEDES_TEMPLATEENGINE_PASS', true);
include(__DIR__ . "index.php");


// Define configuration
$a=array(
	'medes'=>array(
		'version'=>MEDES_VERSION,	
	),
	'site'=>array(
		'default_title'=>'Default title',		
		'prepend_title'=>'PHPMedes: ',		
	),
	'messages' => array(
		'sitetitle' => false,
		'footer' => '<p>This is the footer</p>',
		'copyright' => '<p>&copy; PHPMedes, free and opensource software.</p>',
	),
	//'home' => array('title' => 'The Homepage', 'href' => 'home1'),
	'controllers' => array(
		'index' => array('enabled' => true,'class' => 'CCtrl4Home'),
		'error' => array('enabled' => true,'class' => 'CCtrl4Error'),
		'home' => array('enabled' => true,'class' => 'CCtrl4Home'),
		'user' => array('enabled' => true,'class' => 'CCtrl4User'),
		'developer' => array('enabled' => true,'class' => 'CCtrl4Developer'),
		'themes' => array('enabled' => true,'class' => 'CCtrl4Theme'),
		'cpage' => array('enabled' => true,'class' => 'CCtrl4ContentPage'),
		'canurl' => array('enabled' => true,'class' => 'CCtrl4CanonicalUrl'),
	),
	'menus' => array(
		'list-style' => false,
		'relatedsites' => array(
			'enabled' => true,
			'id' => 'mds-nav-relatedsites',
			'class' => null,
			'items' => array(
				array('text' => 'mikaelroos', 'href' => 'http://mikaelroos.se/', 'class' => '', 'title' => t('Just Me'),),
				array('text' => 'phpmedes', 'href' => 'http://phpmedes.org/', 'class' => '', 'title' => t('Homepage for PHPMedes'),),
				array('text' => 'dbwebb', 'href' => 'http://dbwebb.se/', 'class' => '', 'title' => t('Educational site for development with databases and webapplications'),),
			),
		),
		'login' => array(
			'enabled' => true,
			'id' => 'mds-nav-login',
			'class' => null,
			'callback' => 'CPrinceofPersia::ModifyLoginMenu',
			'items' => array(
			  'login'     => array('text' => 'login', 'href' => 'user/login', 'class' => '', 'title' => 'Login as user',),
				'settings'  => array('text' => 'settings', 'href' => 'user/settings', 'class' => '', 'title' => 'View/edit settings for this account and for this site',),
				'acp'       => array('text' => 'acp', 'href' => 'acp', 'class' => '', 'title' => 'Admin Control Panel',),
				'logout'    => array('text' => 'logout', 'href' => 'user/logout', 'class' => '', 'title' => 'Logout',),
			),
		),
		'main' => array(
			'enabled' => true,
			'id' => 'mds-nav-main',
			'class' => 'mds-nav-main',
			'callback' => 'CPrinceofPersia::ModifyMenuDisplayCurrent',
			'items' => array(
				array('text' => 'home', 'href' => 'home', 'class' => '', 'title' => 'Home',),
				array('text' => 'developer', 'href' => 'developer', 'class' => '', 'title' => 'Aid for the developer',),
				array('text' => 'themes', 'href' => 'themes', 'class' => '', 'title' => 'Aid for the themer',),
				array('text' => 'pages', 'href' => 'cpage/edit', 'class' => '', 'title' => 'pages',),
				array('text' => 'canurls', 'href' => 'canurl/edit', 'class' => '', 'title' => 'canurls',),
			),
		),
	),
	'theme'=> array(
		'name'=>'Medes Core Theme',
		'pathOnDisk'=>MEDES_INSTALL_PATH . '/theme/core',
		'url'=>'theme/core', // Will prepend urlpath to favicon, logo and stylesheets
		'favicon'=>'img/favicon.png',
		'logo'=>array(
			'src'=>'img/logo_medes_330x70.png',
			'alt'=>'Medes Logo',
			'width'=>330,
			'height'=>70,
		),
		'stylesheets'=>array(
      array('file'=>'style/screen.css','type'=>'text/css','media'=>'screen'),
      /* enable to make modifications, and add stylesheets, in site-directory */
/*    'site-mods'=>array('file'=>'../../site/style.css','media'=>'screen'),*/
		),
    /* enable to add own functions to theme */
/*    'functions'=>array('../../site/theme/functions.php'),*/
		'regions'=>array('top-left', 'top-right', 'header', 'navbar-1', 'navbar-2', 'promoted', 'content', 'sidebar1', 'sidebar2', 'triptych1', 'triptych2', 'triptych3', 'footer_column1', 'footer_column2', 'footer_column3', 'footer_column4', 'footer'),
		'developer_tools'=>true,
	),

	// Include these javascript files
	'js'=> array(
		'external'=>array(
/*    'jquery'=>array('src'=>'https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'),
		  'colorbox'=>array('src'=>'site/js/colorbox/colorbox/jquery.colorbox-min.js'), */
		),
		'tracker'=>null,
  ),
);
$b = serialize($a);


// Check if .htaccess exists
echo "<p>Checking .htaccess exists (does not check content though...)...";

if(is_readable(MEDES_INSTALL_PATH . '/.htaccess')) {
  echo "exists in MEDES_INSTALL_PATH...";
} 

echo "OK.</p>";


// Check if data directory exists and is writeable
echo "<p>Checking site/data exists and is writable by webserver...";

if(is_dir(MEDES_DATA_PATH)) {
  echo "directory exists...";
} else {
  echo "directory does not exists... Create it or define MEDES_DATA_PATH.</p>";
  die("<p>Fix the error and then reload this page and try again.</p>");
}

if(is_writable(MEDES_DATA_PATH)) {
  echo "directory is writable by webserver...";
} else {
  echo "Failed. Make directory writable by webserver.</p>";
  die("<p>Fix the error and then reload this page and try again.</p>");
}

echo "OK.</p>";


// Set default date/time-zone
echo "<p>Set default date/time-zone...";
date_default_timezone_set($pp->cfg['server']['timezone']);
echo "OK.</p>";


// Create the main database, where the Medes configuration is.
echo "<p>Create/open the database...";

if(isset($pp->cfg['db'][0]['dsn'])) {
  echo "database dsn is set...";
} else {
  echo "No entry for first database exists in config.php. Edit config.php.</p>";
  die("<p>Fix the error and then reload this page and try again.</p>");
}

echo "creating database object...";
extract($pp->cfg['db'][0]);
try {
  $pp->db = new CDatabaseController($dsn, $username, $password, $driver_options);
} 
catch(Exception $e){
  echo "Could not create database object. Check database settings in config.php.</p>";
  die("<p>Fix the error and then reload this page and try again.</p>");
}
echo "OK.</p>";


// Database queries, copied from CPrinceOfPersia
$query = array(
  'create table pp' => 'create table if not exists pp(module text, key text, value text, primary key(module, key))',
  'load pp:config' => 'select value from pp where module="CPrinceOfPersia" and key="config"',
  'save pp:config' => 'update pp set value=? where module="CPrinceOfPersia" and key="config"',
  'create pp:config' => 'insert into pp (module, key, value) values ("CPrinceOfPersia", "config", ?)',
);


// Create table if not exists
echo "<p>Creating/Checking database table for \$pp...";
$pp->db->ExecuteQuery($query['create table pp']);
echo "OK.</p>";

echo "<p>Creating/Checking database table for users...";
$o = CUserController::GetInstance();
$o->InstallModule();
echo "OK.</p>";

echo "<p>Creating/Checking database table for articles...";
$o = new CArticle();
$o->InstallModule();
echo "OK.</p>";

echo "<p>Creating/Checking database table for canurls...";
$o = new CCanonicalUrl();
$o->InstallModule();
echo "OK.</p>";


// Updating configuration to database
if(isset($_GET['update'])) {
  echo "<p>Updating config to database...";
  $pp->db->ExecuteQuery($query['save pp:config'], array($b));
  echo "rows updated=" . $pp->db->RowCount() . "...";
  echo "OK.</p>";
}


// Try to load config from database
echo "<p>Loading config from database...";

$cfg = $pp->db->ExecuteSelectQueryAndFetchAll($query['load pp:config']);
if(empty($cfg)) {
  echo "config does not exists, inserting configuration...";
  $pp->db->ExecuteQuery($query['create pp:config'], array($b));
  echo "loading config again...";
  $cfg = $pp->db->ExecuteSelectQueryAndFetchAll($query['load pp:config']);
}
$pp->cfg['config-db'] = unserialize($cfg[0]['value']);
echo "OK.</p>";
echo "<p>Current configuration is:</p><pre>", print_r($pp->cfg['config-db'], true), "</pre>";



// For all classes, check if module IInstallable, call method for install

