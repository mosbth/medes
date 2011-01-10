<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8">

	<!-- Use meta to ease indexing made by search engines -->
	<meta name="keywords"    content="<?php echo $pp->pageKeywords; ?>">
 	<meta name="description" content="<?php echo $pp->pageDescription; ?>">
 	<meta name="author"      content="<?php echo $pp->pageAuthor; ?>">	
 	<meta name="copyright"   content="<?php echo $pp->pageCopyright; ?>">	
 		
	<!-- Use PHP to set the page title dynamic -->
 	<?php echo "<title>$pp->pageTitle</title>"; ?>
 	
	<!-- Stylesheets and style-->
	<?php echo $pp->GetHTMLForStyle(); ?>

	<!-- The small icon displayed by the browser next to the link -->
	<link rel="shortcut icon" type="img/png" href="<?php echo $pp->PrependWithSiteUrl('img/favicon.png'); ?>">

	<!-- Help Internet Explorer understand HTML5 elements http://code.google.com/p/html5shiv/ -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Support Google Analytics -->
	<?php if(!empty($pp->googleAnalytics)) echo $pp->googleAnalytics; ?>

</head>
