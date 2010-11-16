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
	<?php //include($pp->medesPath . "/inc/choose_style.php"); ?>
	<link rel="stylesheet" media="all"   type="text/css" href="<?php echo $pp->GetLinkToStylesheet(); ?>">
	<link rel="stylesheet" media="print" type="text/css" href="<?php echo $pp->PrependWithSiteUrl('medes/style/print.css'); ?>">

	<!-- The small icon displayed by the browser next to the link -->
	<link rel="shortcut icon" href="img/favicon.ico">

	<!-- Use PHP to set the page title dynamic -->
 	<?php echo "<title>$pp->pageTitle</title>"; ?>
 	
	<!-- Set inline style if defined -->
 	<?php if(!empty($pp->pageInlineStyle)) echo "<style type='text/css'>{$pp->pageInlineStyle}</style>"; ?>
 	
	<!-- Help Internet Explorer understand HTML5 elements http://code.google.com/p/html5shiv/ -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Support Google Analytics -->
	<?php if(!empty($pp->googleAnalytics)) echo $pp->googleAnalytics; ?>

</head>
<body>

<!-- Get the header of the site, including the main navbar -->
<?php echo $pp->GetHTMLForHeader(); ?>

<!-- Here is the actual content of the page-->
<div id=content>
