<?php 
require_once("config.php");
$pp->pageTitle = "Template";
//$pp->Dump();
include($pp->medesPath . "/inc/header.php");
?>

<article>
<h1>Create a page using template.php</h1>
<p>Copy this file, save it with a new name and edit its content to create a new webpage.
</article>

<?php include($pp->medesPath . "/inc/footer.php"); ?>