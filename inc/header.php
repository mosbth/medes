<?php 
include(dirname(__FILE__) . "/../config.php");
if(!empty($cfgSessionName)) {
	session_name($cfgSessionName); 
	session_start(); 
}
error_reporting($cfgErrorReporting);
include(dirname(__FILE__) . "/../src/functions.php");
?>

<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8">

	<!-- Use meta to ease indexing made by search engines -->
	<meta name="keywords"    content="<?php echo isset($pageKeywords) ? $pageKeywords : $cfgPageKeywords; ?>">
 	<meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : $cfgPageDescription; ?>">
 	<meta name="author"      content="<?php echo isset($pageAuthor) ? $pageAuthor : $cfgPageAuthor; ?>">	
 	<meta name="copyright"   content="<?php echo isset($pageCopyright) ? $pageCopyright : $cfgPageCopyright; ?>">	
 		
	<!-- Stylesheets -->
	<?php include(dirname(__FILE__) . "/choose_style.php"); ?>
	<link rel="stylesheet" media="all"   type="text/css" href="<?php echo $stylePath; ?>"  		title="<?php echo $styleTitle; ?>">
	<link rel="stylesheet" media="print" type="text/css" href="style/print.css">

	<!-- The small icon displayed by the browser next to the link -->
	<link rel="shortcut icon" href="img/favicon.ico">

	<!-- Use PHP to set the page title dynamic -->
 	<?php echo "<title>$pageTitle</title>"; ?>
 	
	<!-- Help Internet Explorer understand HTML5 elements http://code.google.com/p/html5shiv/ -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Support Google Analytics -->
	<?php if(!empty($cfgGoogleAnalytics)) echo $cfgGoogleAnalytics; ?>

</head>

<!-- Use PHP to set id of body, used to highlight current page, together with styling information -->
<body<?php  if(!empty($pageBodyId)) echo " id='$pageBodyId'"; ?>>

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
		<a id="about-"  					href="about.php">about</a> 
		<a id="documentation-"   	href="documentation.php">documentation</a> 
		<a id="template-"   			href="template.php">template</a> 
		<a id="style-"   					href="style.php">style</a> 
		<a id="admin-"  					href="admin/admin.php">admin</a> 
	</nav>
