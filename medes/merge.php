<?php 
$config1 = unserialize(file_get_contents("data/CPrinceOfPersia_config.php"));
$config2 = unserialize(file_get_contents("data/CPrinceOfPersia_config_backup.php"));

$config1['header'] = $config2['header'];
$config1['footer'] = $config2['footer'];
$config1['password'] = $config2['password'];

$config1['navigation']['navbar'] = array(
	"text"=>"Main navigation",
	"nav"=>$config2['navbar'],
);
$config1['navigation']['relatedsites'] = array(
	"text"=>"Top left menu",
	"nav"=>$config2['relatedsites'],
);

file_put_contents("data/CPrinceOfPersia_config.php", serialize($config1));
