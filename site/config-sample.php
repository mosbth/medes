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
 * Add database settings. Used to setup the PDO database object.
 * @see PDO::__construct()
 */
$pp->cfg['db']['dsn'] = 'sqlite:' . MEDES_SITE_PATH . '/data/._htdb.sqlite';
$pp->cfg['db']['username'] = null;
$pp->cfg['db']['password'] = null;
$pp->cfg['db']['driver_options'] = null;

/*
 * Define siteurl other than default. Set to null to disable and let $pp figure it out.
 */
$pp->cfg['general']['siteurl'] = null;

/*
 * Define session name
 */
$pp->cfg['session']['name'] = preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]);

/*
 * Define server timezone
 */
$pp->cfg['server']['timezone'] = 'Europe/Stockholm';


