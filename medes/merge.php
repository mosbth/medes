<?php 
require_once("config.php");

$config = unserialize(file_get_contents($this->medesPath . "/data/CPrinceOfPersia_config_backup.php"));

$pp->config['header'] = $config['header'];
$pp->config['footer'] = $config['footer'];
$pp->config['password'] = $config['password'];

$pp->config['navigation']['navbar'] = array(
	"text"=>"Main navigation",
	"nav"=>$config['navbar'],
);
$pp->config['navigation']['relatedsites'] = array(
	"text"=>"Top left menu",
	"nav"=>$config['relatedsites'],
);
$pp->Dump;
