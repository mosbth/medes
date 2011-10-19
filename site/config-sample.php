<?php
/**
 * Site configuration.
 *
 * This file is included from $pp->Config. Add values directly to the array $pp->cfg which contains
 * all settings.
 *
 * @package MedesSite
 */

/*
 * Set level of error reporting
 */
error_reporting(-1); 

/*
 * The path to the writable data dorectory, for storage, databases and uploaded files.
 */
define('MEDES_DATA_PATH', MEDES_SITE_PATH . '/data');

/*
 * Add database settings. Used to setup the PDO database object.
 * @see PDO::__construct()
 */
$pp->cfg['db'][0]['dsn'] = 'sqlite:' . MEDES_DATA_PATH . '/.htdb.sqlite';
$pp->cfg['db'][0]['username'] = null;
$pp->cfg['db'][0]['password'] = null;
$pp->cfg['db'][0]['driver_options'] = null;

/*
 * Define siteurl other than default. Set to null to disable and let $pp figure it out.
 */
$pp->cfg['general']['base_url'] = null;
//$pp->cfg['general']['base_url'] = 'http://example.com/medes';

/*
 * Define session name
 */
$pp->cfg['session']['name'] = preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]);

/*
 * Define server timezone
 */
$pp->cfg['server']['timezone'] = 'Europe/Stockholm';

/*
 * Set internal character encoding to UTF-8
 */
$pp->cfg['general']['character_encoding'] = 'UTF-8';

