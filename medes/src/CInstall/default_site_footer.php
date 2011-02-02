<?php 
// ---------------------------------------------------------------------
//
// php-code to create html-footer of the site
//
$pp = CPrinceOfPersia::GetInstance(); 
$developer = $pp->GetHTMLForDeveloperMenu();

$html = <<<EOD
</div> <!-- end of div#content -->
<footer id="bottom-wrap">
<footer id="bottom" class="container">

<!--
	<p class="span-24 last">The content on this site is licensed according to  
	<a href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution Share-Alike License v3.0</a>
	or any later version. 
	</p>
-->

	<p class="span-24 last">This site is built using <a href="http://phpmedes.org/">the free 
	and opensource software named phpmedes</a>. PhpMedes is free software, open source, and therefore licensed 
	according to <a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License version 3</a>.
	</p>
	
	{$developer}

</footer> <!-- end of #bottom -->
</footer> <!-- end of #bottom-wrap -->
</body>
</html>

EOD;

echo $html;
