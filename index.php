<?php
/**
 * Setting up Medes, managing the request, prepares the reply and serves it.
 *
 * A page request is handled in the following way:
 * 1. Bootstrapping, setting up and checking the environment.
 * 2. Frontcontroller, forward request to pagecontrollers which prepares a set of views.
 * 3. Template engine, renders the views onto choosen theme.
 *
 * @package MedesCore
 */

// ------------------------------ PHASE: Bootstrapping -------------------------------------------

/**
 * The Medes version.
 */
define('MEDES_VERSION', 'v0.18 latest');

/**
 * The path to the Medes installation directory.
 */
define('MEDES_INSTALL_PATH', __DIR__);

// Define the path to the sites directory
if(!defined('MEDES_SITE_PATH')) {
	/**
	 * The path to the Medes installation directory.
	 *
	 * Override by defining it in a site's index.php, before including Medes index.php.
	 */
	define('MEDES_SITE_PATH', MEDES_INSTALL_PATH . '/site');
}

/*
 * use bootstrap.php to set up additional items
 */
include(MEDES_INSTALL_PATH . '/src/CPrinceOfPersia/bootstrap.php');

/*
 * Get/Create the instance of the master of Medes, the PrinceOfPersia.
 *
 * Using defines available in this file during setup, everything else can be configured in a 
 * config.php on per site basis.
 */
if(!(defined('MEDES_INSTANTIATE_PASS') && MEDES_INSTANTIATE_PASS == true)) {
  $pp = CPrinceOfPersia::GetInstance();
}

// ------------------------------ PHASE: Frontcontroller -----------------------------------------

/*
 * Use frontcontroller to manage request, forward to choosen controllers.
 */
if(!(defined('MEDES_FRONTCONTROLLER_PASS') && MEDES_FRONTCONTROLLER_PASS == true)) {
	$pp->FrontControllerRoute();
}

// ------------------------------ PHASE: Template engine -----------------------------------------

/*
 * Render the response by mapping all views to the theme.
 */
if(!(defined('MEDES_TEMPLATEENGINE_PASS') && MEDES_TEMPLATEENGINE_PASS == true)) {
	$pp->TemplateEngineRender();
}
