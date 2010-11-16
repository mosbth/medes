<?php $pp = CPrinceOfPersia::GetInstance(); ?>

<footer id="bottom">

	<nav>
		<h5>phpmedes</h5>
			<a href='http://phpmedes.org/'>phpmedes.org</a>
	</nav>
	
	<nav>
		<h5>Tools</h5>
		<a href="http://validator.w3.org/check/referer">HTML5</a> 
		<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">CSS3</a>
		<a href="http://validator.w3.org/unicorn/check?ucn_uri=referer&#38;ucn_task=conformance">Unicorn</a>
		<a href="http://www.w3.org/2009/cheatsheet/">Cheatsheet</a>
		<a href="http://validator.w3.org/checklink?uri=<?php echo $pp->GetUrlToCurrentPage(); ?>">Link Checker</a>
		<a href="http://qa-dev.w3.org/i18n-checker/index?async=false&#38;docAddr=<?php echo $pp->GetUrlToCurrentPage(); ?>">i18n Checker</a>
		<a href="http://web-sniffer.net/?url=<?php echo $pp->GetUrlToCurrentPage(); ?>">Check header</a>
	</nav>
	
	<nav>
		<h5>Manuals</h5>
		<a href="http://dev.w3.org/html5/spec/spec.html">HTML5</a> 
		<a href="http://www.w3.org/TR/CSS2/">CSS2</a> 
		<a href="http://www.w3.org/Style/CSS/current-work#CSS3">CSS3</a> 
		<a href="http://php.net/manual/en/index.php">PHP</a> 
		<a href="http://www.sqlite.org/lang.html">SQL (SQLite)</a>
	</nav>

	<p class=license>
	The content on this site is licensed according to  
	<a href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution Share-Alike License v3.0</a>
	or any later version. The software phpmedes is free software, open source, licensed according to 
	<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License version 3</a>.
	</p>
	
	<p class=medes>This site is built using <a href="http://phpmedes.org/">the free and opensource software named phpmedes</a>.</p>

</footer>
