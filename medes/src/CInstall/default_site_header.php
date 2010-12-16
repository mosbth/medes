<?php 
// ---------------------------------------------------------------------
//
// php-code to create html-header of the site
//
$pp = CPrinceOfPersia::GetInstance(); 
$relatedSites = $pp->GetHTMLForRelatedSitesMenu();
$profile = $pp->GetHTMLForProfileMenu();
$logo = $pp->PrependWithSiteUrl('img/logo_medes_335x70.png');
$logoLink = $pp->PrependWithSiteUrl('medes/template.php');
$navbar = $pp->GetHTMLForNavbar();

$html = <<<EOD
<html>
<header id=top-above>
	{$profile}
	{$relatedSites}
</header>

<header id=top class="container showgrid" style="overflow:visible;">
	<a href="{$logoLink}"><img src="{$logo}" alt="Logo" width=335 height=70 style="margin-left:0px;"></a>
{$navbar}
</header>

<!-- Here is the actual content of the page-->
<div id=content class="container showgrid prepend-top append-bottom">

EOD;

echo $html;
