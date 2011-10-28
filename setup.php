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
  define('MEDES_SITE_PATH', __DIR__ . '/site');
}

$errmsg = "<p>Fix the error and then reload this page and try again.</p>";


//
// Check if config.php exists, its essential
//
echo "<h1>PHPMedes setup</h1><p>File <code>site/config.php</code> exists... ";
if(is_readable(MEDES_SITE_PATH . '/config.php')) {
  echo "OK.";
} else {
  echo "NOK.</p><p><b>The file does not exists.</b> Copy <code>site/config-sample.php</code> to <code>site/config.php</code>, review it and edit if needed.</p>";
  die($errmsg);
}


//
// Start it up
// Use Medes bootstrap to gain access to medes environment
// disgard the rest
//
define('MEDES_INSTANTIATE_PASS', true);
define('MEDES_FRONTCONTROLLER_PASS', true);
define('MEDES_TEMPLATEENGINE_PASS', true);
include(__DIR__ . "/index.php");
date_default_timezone_set($pp->cfg['server']['timezone']);


//
// Get the configuration
//
echo "<p>File <code>site/config_setup.php</code> exists... ";
if(is_readable(MEDES_SITE_PATH . '/config_setup.php')) {
  echo "OK.</p>";
} else {
  echo "NOK.</p><p><b>The file does not exists.</b> Copy <code>site/config_setup-sample.php</code> to <code>site/config_setup.php</code>, review it and edit if needed.</p>";
  die($errmsg);
}

include(MEDES_SITE_PATH . '/config_setup.php');
$b = serialize($cfg);


//
// Check if .htaccess exists
//
echo "<p>File <code>.htaccess</code> exists...";

if(is_readable(MEDES_INSTALL_PATH . '/.htaccess')) {
  echo "OK.</p>";
} else {
  echo "NOK.</p>";
  die($errmsg);
}


//
// Check if data directory exists and is writeable
//
echo "<p>Directory <code>site/data</code> exists...";
if(is_dir(MEDES_DATA_PATH)) {
  echo "OK.</p>";
} else {
  echo "NOK.</p><p><b>The directory <code>site/data</code> does not exists.</b> Create it or define MEDES_DATA_PATH.</p>";
  die($errmsg);
}

echo "<p>Directory <code>site/data</code> is writable by webserver...";
if(is_writable(MEDES_DATA_PATH)) {
  echo "OK.</p>";
} else {
  echo "NOK.</p><p><b>The directory <code>site/data</code> is not writable by the webserver.</b> Make directory writable by webserver.</p>";
  die($errmsg);
}


//
// Create the main database, where the Medes configuration is.
//
echo "<p>Verify database settings from <code>site/config.php</code>...";
if(isset($pp->cfg['db'][0]['dsn'])) {
  echo "OK.</p>";
} else {
  echo "NOK.</p><p><b>No entry for first database exists in <code>site/config.php</code>.</b> Edit the file and correct the database entry.</p>";
  die($errmsg);
}

echo "Initiating database object...";
extract($pp->cfg['db'][0]);
try {
  $pp->db = new CDatabaseController($dsn, $username, $password, $driver_options);
} 
catch(Exception $e){
  echo "NOK.</p><p><b>Could not create database object.</b> Check your database settings in <code>site/config.php</code>.</p>";
  die($errmsg);
}
echo "OK.</p>";


//
// Database queries, copied from CPrinceOfPersia
//
$query = array(
  'create table pp'   => 'create table if not exists pp(module text, key text, value text, primary key(module, key))',
  'load pp:config'    => 'select value from pp where module="CPrinceOfPersia" and key="config"',
  'save pp:config'    => 'update pp set value=? where module="CPrinceOfPersia" and key="config"',
  'create pp:config'  => 'insert into pp (module, key, value) values ("CPrinceOfPersia", "config", ?)',
);


//
// For all classes, check if module IInstallable, call method for install
//
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


//
// Updating configuration to database
//
if(isset($_GET['update'])) {
  echo "<p>Updating config to database...";
  $pp->db->ExecuteQuery($query['save pp:config'], array($b));
  echo "rows updated=" . $pp->db->RowCount() . "...";
  echo "OK.</p>";
}


//
// Try to load config from database
//
echo "<p>Loading config from database...";

$cfg = $pp->db->ExecuteSelectQueryAndFetchAll($query['load pp:config']);
if(empty($cfg)) {
  echo "config does not exists, inserting new configuration...";
  $pp->db->ExecuteQuery($query['create pp:config'], array($b));
  echo "loading config again...";
  $cfg = $pp->db->ExecuteSelectQueryAndFetchAll($query['load pp:config']);
}
$pp->cfg['config-db'] = unserialize($cfg[0]['value']);
echo "OK.</p>";
echo "<p><a href='index.php'>Visit the site</a>.";
echo "<p><a href='?update'>Save new configuration to database (I have made updates to the file: <code>site/config_setup.php</code></a>).";
echo "<p>Current configuration is:</p><pre>", print_r($pp->cfg['config-db'], true), "</pre>";




