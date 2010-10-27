<?php
require_once("../config.php");
$pp->pageTitle = "Download";
//$pp->Dump();
include($pp->medesPath . "/inc/header.php");
?>


<article>
<h1>Download</h1>

<h2>Get the code</h2>
<p>phpmedes is hosted at GitHub.

<p>There are currently no stable version. The plan is to reach the first stable version on december 23, 2010.

<p>The latest development version can be downloaded from <a href="http://github.com/mosbth/medes">http://github.com/mosbth/medes</a>

<h2>Get going</h2>
<ol>
<li>Download and install at the webserver.
<li>Create the config.php by:<br>
<code>cp medes/config-sample.php medes/config.php</code>
<li>Edit the config.php and set the sitelink.
<li>Set you browser to point at the index-page.
</ol>

<h2>Create a new page</h2>
<p>Create a new page by:<br>
<code>cp medes/doc/template.php newpage.php</code>

<p>Edit the new page and ensure to point out the location of config.php at row 2. 

<p>
</article>

<?php include($pp->medesPath . "/inc/footer.php"); ?>