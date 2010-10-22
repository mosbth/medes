<?php
require_once("../config.php");
$cfg->pageTitle = "Addons";
//$cfg->Dump();
include($cfg->medesPath . "/inc/header.php");
?>


<article>
<h1>Addons</h1>

<p>The idea is to have a small kernel of phpmedes-code and extend it using addons. There
need to be a stable version before develpoment of addons can start. 

<p>The first addon, a blog,
will be included in phpmedes and used as an example on how to build an addon.

<p>Until then, keep low and be prepared.

</p>
</article>

<?php include($cfg->medesPath . "/inc/footer.php"); ?>