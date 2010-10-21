<?php 
require_once("../config.php");
$cfg->pageTitle = "Template";
//$cfg->Dump();
include($cfg->medesPath . "/inc/header.php");
?>

<h1>Create a page using template.php</h1>
<p>Copy this file, save it with a new name and edit its content to create a new webpage.


<?php include($cfg->medesPath . "/inc/footer.php"); ?>