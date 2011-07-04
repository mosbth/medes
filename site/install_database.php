<?php

// Use Medes bootstrap to gain access to fully populated $pp
define('MEDES_FRONTCONTROLLER_PASS', true);
define('MEDES_TEMPLATEENGINE_PASS', true);
//include(__DIR__ . "/../index.php");

// For all classes, check if module IInstallable, call method for install

// include the site specific config.php
include('config.php');

// Create the main database, where the Medes configuration is.
extract($this->cfg['db'][0]);
$db = new CDatabaseController($dsn, $username, $password, $driver_options);

// Set configuration
$a=array(
	'medes'=>array(
		'version'=>MEDES_VERSION,	
	),
	'messages' => array(
		'copyright' => '&copy; PHPMedes, free and opensource software.',
	),
	'controllers' => array(
		'index' => array('enabled' => true,'class' => 'CCtrl4Home'),
		'error' => array('enabled' => true,'class' => 'CCtrl4Error'),
		'home' => array('enabled' => true,'class' => 'CCtrl4Home'),
		'user' => array('enabled' => true,'class' => 'CCtrl4User'),
		'developer' => array('enabled' => true,'class' => 'CCtrl4Developer'),
		'theme' => array('enabled' => true,'class' => 'CCtrl4Theme'),
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
				array(
					'text' => 'mikaelroos',
					'href' => 'http://mikaelroos.se/',
					'class' => '',
					'title' => t('Just Me'),
				),
				array(
					'text' => 'phpmedes',
					'href' => 'http://phpmedes.org/',
					'class' => '',
					'title' => t('Homepage for PHPMedes'),
				),
				array(
					'text' => 'dbwebb',
					'href' => 'http://dbwebb.se/',
					'class' => '',
					'title' => t('Educational site for development with databases and webapplications'),
				),
			),
		),
		'login' => array(
			'enabled' => true,
			'id' => 'mds-nav-login',
			'class' => null,
			'callback' => 'CPrinceofPersia::ModifyLoginMenu',
			'items' => array(
				'login' => array(
					'text' => 'login',
					'href' => 'user/login',
					'class' => '',
					'title' => 'Login as user',
				),
				'settings' => array(
					'text' => 'settings',
					'href' => 'user/settings',
					'class' => '',
					'title' => 'View/edit settings for this account and for this site',
				),
				'acp' => array(
					'text' => 'acp',
					'href' => 'acp',
					'class' => '',
					'title' => 'Admin Control Panel',
				),
				'logout' => array(
					'text' => 'logout',
					'href' => 'user/logout',
					'class' => '',
					'title' => 'Logout',
				),
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
				array('text' => 'theme', 'href' => 'theme', 'class' => '', 'title' => 'Aid for the themer',),
				array('text' => 'pages', 'href' => 'cpage/edit', 'class' => '', 'title' => 'pages',),
				array('text' => 'canurls', 'href' => 'canurl/edit', 'class' => '', 'title' => 'canurls',),
			),
		),
	),
	'theme'=> array(
		'name'=>'Medes Core Theme',
		'pathOnDisk'=>__DIR__ . '/medes_latest/theme/core',
		'url'=>'medes_latest/theme/core',
		'logo'=>array(
			'src'=>'img/logo_medes_330x70.png',
			'alt'=>'Medes Logo',
			'width'=>330,
			'height'=>70,
		),
		'favicon'=>'img/favicon.png',
		'stylesheets'=>array(
			array(
				'file'=>'screen.css',
				'type'=>'text/css',
				'media'=>'screen',
			),
		),
		'regions'=>array('top-left', 'top-right', 'header', 'navbar-1', 'navbar-2', 'promoted', 'content', 'sidebar1', 'sidebar2', 'triptych1', 'triptych2', 'triptych3', 'footer_column1', 'footer_column2', 'footer_column3', 'footer_column4', 'footer'),
	),
);
$b = serialize($a);

$q = 'update pp set value=? where module="CPrinceOfPersia" and key="config"';
$db->ExecuteQuery($q, array($b));


