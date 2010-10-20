<?php
// ===========================================================================================
//
// config.php (config-sample.php)
//
// General configurations for the site. Copy config-sample.php to config.php and edit it.
//

// -------------------------------------------------------------------------------------------
//
// Sessions
//
// The sessions is started in the file inc/header.php
//
//$cfgSessionName = ''; // An empty value disables the use of sessions
$cfgSessionName = 'a_named_session';


// -------------------------------------------------------------------------------------------
//
// Error reporting
//
// The following value is used when calling error_reporting() in inc/header.php
//
//$cfgErrorReporting = 0; // Disable error reporting
$cfgErrorReporting = -1; // Full error reporting


// -------------------------------------------------------------------------------------------
//
// Default meta-tags
//
// Set the default meta tags for this site. Each page can override them by setting individual 
// values in the following variables:
//  $pageKeywords
//  $pageDescription
//  $pageAuthor 
//  $pageCopyright
//
// Review the file inc/header.php to see how the variables are used.
//
$cfgPageKeywords 		= 'General keywords for this site/page';
$cfgPageDescription	= 'General description for this site/page';
$cfgPageAuthor 			= 'Author of this site/page';
$cfgPageCopyright 	= 'Copyright for this site/page';


// -------------------------------------------------------------------------------------------
//
// Google Analytics
//
// Are you using Google Analytics to track visits of this site? Then put the Javascript here.
// It will be output just before the </head>-tag in the file inc/header.php.
// 
// $cfgGoogleAnalytics = ''; // An empty string will disable this feature.
//
$cfgGoogleAnalytics = <<<EOD
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-6902244-5']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
EOD;



// Omitting PHP end-tag by purpose