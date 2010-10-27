<?php 
require_once("../config.php");
$pp->pageTitle = "Siteadmin";
//$pp->Dump();
include($pp->medesPath . "/inc/header.php");
?>

<aside><?php include("menu.php"); ?></aside>

<article>
<h1>Admin Area</h1>
<p>Here you can change global settings of the site.

<?php echo $pp->GainOrLooseAdminAccess(); ?>

</article>

<?php include($pp->medesPath . "/inc/footer.php"); ?>