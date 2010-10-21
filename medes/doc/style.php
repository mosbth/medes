<?php
require_once("../config.php");
$cfg->pageTitle = "Style your phpmedes";
//$cfg->Dump();
include($cfg->medesPath . "/inc/header.php");
?>

<article>
<h1>Change style</h1>

<p>Choose from the existing stylesheets or create your own personlized style by editing 
the stylesheet.

<p>Silver, color1-6:
<div class=color style="background:#222"></div>
<div class=color style="background:#444"></div>
<div class=color style="background:#999"></div>
<div class=color style="background:#aaa"></div>
<div class=color style="background:#ccc"></div>
<div class=color style="background:#eee"></div>

<p style="clear:both;padding-top:1em;">Redish, color1-6:
<div class=color style="background:#300"></div>
<div class=color style="background:#500"></div>
<div class=color style="background:#f55"></div>
<div class=color style="background:#f88"></div>
<div class=color style="background:#faa"></div>
<div class=color style="background:#fee"></div>

<p style="clear:both;padding-top:1em;">Green, color1-6:
<div class=color style="background:#030"></div>
<div class=color style="background:#050"></div>
<div class=color style="background:#5f5"></div>
<div class=color style="background:#8f8"></div>
<div class=color style="background:#afa"></div>
<div class=color style="background:#efe"></div>

<p style="clear:both;padding-top:1em;">Blue, color1-6:
<div class=color style="background:#003"></div>
<div class=color style="background:#005"></div>
<div class=color style="background:#55f"></div>
<div class=color style="background:#88f"></div>
<div class=color style="background:#aaf"></div>
<div class=color style="background:#eef"></div>
</article>
        
</div> <!-- content -->

<?php include($cfg->medesPath . "/inc/footer.php"); ?>