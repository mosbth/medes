<?php echo $this->GetHTMLDocType(); ?>
<head>
	<!-- Meta tags -->
	<?php echo $this->GetHTMLForMeta(); ?>

	<!-- Use PHP to set the page title dynamic -->
 	<?php echo "<title>$this->pageTitle</title>"; ?>
 	
	<!-- Stylesheets and style -->
	<?php echo $this->GetHTMLForStyle(); ?>

	<!-- Script -->
	<?php echo $this->GetHTMLForScript(); ?>

	<!-- Help Internet Explorer understand HTML5 elements http://code.google.com/p/html5shiv/ -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Support Google Analytics -->
	<?php if(!empty($this->googleAnalytics)) echo $this->googleAnalytics; ?>

</head>
