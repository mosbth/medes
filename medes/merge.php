<?php 
require_once("config.php");

$config = unserialize(file_get_contents($this->medesPath . "/data/{$className}_config_backup.php"));

$pp->config['navigation']['navbar'] = array(
	"text"=>"Main navigation",
	"nav"=>$config['navbar'];
);
$pp->Dump;
