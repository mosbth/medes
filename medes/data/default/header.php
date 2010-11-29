<?php 
// ---------------------------------------------------------------------
//
// php-code to create html-header of the site
//
$pp = CPrinceOfPersia::GetInstance(); 
$relatedSites = $pp->GetHTMLForRelatedSitesMenu();
$profile = $pp->GetHTMLForProfileMenu();
$logo = $pp->PrependWithSiteUrl('img/logo_medes_335x70.png');
$navbar = $pp->GetHTMLForNavbar();

$html = <<<EOD
<header id=top-above>
	{$profile}
	{$relatedSites}
</header>

<header id=top class="container showgrid" style="overflow:visible;">
	<img src="{$logo}" alt="Logo" width=335 height=70 style="margin-left:0px;">
{$navbar}
</header>

<!-- Here is the actual content of the page-->
<div id=content class="container showgrid prepend-top append-bottom">

EOD;

echo $html;
