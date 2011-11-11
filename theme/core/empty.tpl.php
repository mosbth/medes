<?php echo $pp->GetHTMLDocType(); ?>
<head>
	<!-- Meta tags -->
	<?php echo $pp->GetHTMLForMeta(); ?>

	<!-- Use PHP to set the page title dynamic -->
 	<?php echo $pp->GetHTMLForPageTitle(); ?>
 	
	<!-- Stylesheets and style -->
	<?php echo $pp->GetHTMLForStyle(); ?>

	<!-- Favicon -->
	<?php echo $pp->GetHTMLForFavicon(); ?>

	<!-- Script -->
	<?php echo $pp->GetHTMLForScript(); ?>

	<!-- Help Internet Explorer understand HTML5 elements http://code.google.com/p/html5shiv/ -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>
<body>
<?php echo $pp->RenderViewsForRegion('content'); ?>
</body>
</html>