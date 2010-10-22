<?php
require_once("../config.php");
$cfg->pageTitle = "About";
//$cfg->Dump();
include($cfg->medesPath . "/inc/header.php");
?>

<article>
<h1>About</h1>

<p>phpmedes is a free and opensource software which helps to quickly build small websites. Its
built using PHP, HTML and CSS.

<p>phpmedes.org is the site hosting the opensource development project of the free software named phpmedes.

<p>Founders, and current lead-developers, are Mikael Roos and Rickard Gimerstedt.
</p>
</article>

<?php include($cfg->medesPath . "/inc/footer.php"); ?>