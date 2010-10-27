<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8">

	<!-- Use meta to ease indexing made by search engines -->
	<meta name="keywords"    content="<?php echo $pp->pageKeywords; ?>">
 	<meta name="description" content="<?php echo $pp->pageDescription; ?>">
 	<meta name="author"      content="<?php echo $pp->pageAuthor; ?>">	
 	<meta name="copyright"   content="<?php echo $pp->pageCopyright; ?>">	
 		
	<!-- Stylesheets -->
	<?php include($pp->medesPath . "/inc/choose_style.php"); ?>
	<link rel="stylesheet" media="all"   type="text/css" href="<?php echo $pp->siteUrl . $stylePath; ?>"  		title="<?php echo $styleTitle; ?>">
	<link rel="stylesheet" media="print" type="text/css" href="style/print.css">

	<!-- The small icon displayed by the browser next to the link -->
	<link rel="shortcut icon" href="img/favicon.ico">

	<!-- Use PHP to set the page title dynamic -->
 	<?php echo "<title>$pp->pageTitle</title>"; ?>
 	
	<!-- Help Internet Explorer understand HTML5 elements http://code.google.com/p/html5shiv/ -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Support Google Analytics -->
	<?php if(!empty($pp->googleAnalytics)) echo $pp->googleAnalytics; ?>

</head>
<body>

<!-- Top header with logo and navigation -->
<header id="top">

	<!-- Use PHP to print the form to choose style -->
	<?php echo $formChooseStyle; ?>

	<!-- Default phpmedes-logo -->
	<div class=logo>
		<p id=label>phpmedes
		<p id=tagline>dbwebb.se
	</div>

	<!-- Use an image as logo -->
	<!-- <img src="img/logo.png" alt="Logo" width=200 height=100> -->
	
</header>

<!-- Top navigation bar -->
<nav class=mainmenu>
<!--
	<a href="<?php echo $pp->siteUrl . "/blog"; ?>">blog</a> 
-->
	<a href="<?php echo $pp->siteUrl . "/medes/doc/home.php"; ?>">home</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/showcase.php"; ?>">showcase</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/features.php"; ?>">features</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/style.php"; ?>">style</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/addons.php"; ?>">addons</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/download.php"; ?>">download</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/contribute.php"; ?>">contribute</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/docs.php"; ?>">docs</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/blog.php"; ?>">blog</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/doc/about.php"; ?>">about</a> 
	<a href="<?php echo $pp->siteUrl . "/medes/adm/home.php"; ?>">adm</a> 
</nav>


<!-- Here is the actual content of the page-->
<div id=content>
