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
<body>

<!-- top -->
<div id="mds-top-wrap" class="clearfix">
 <div id="mds-top-left"><?php echo $pp->GetHTMLForRelatedSitesMenu(); ?></div>
 <div id="mds-top-right"><?php echo $pp->GetHTMLForLoginMenu(); ?></div>
</div>


<!-- header -->
<div id="mds-header-wrap">
 <div id="mds-header" class="container">

  <div id="mds-header-logo">
   <a href="<?php echo $pp->PrependWithSiteUrl('medes/page/template.php') ?>"><img src="<?php echo $pp->PrependWithSiteUrl('img/logo_medes_335x70.png'); ?>" alt="Logo" width="335" height="70" style="margin-left:0px;"/></a>
  </div>

  <div id="mds-header-title"></div>

  <div id="mds-header-menu"><?php echo $pp->GetHTMLForMainMenu(); ?></div>

 </div>
</div>


<!-- content -->
<div id="mds-content-wrapper" class="prepend-top append-bottom">

 <div class="mds-content-row-wrapper">
  <div class="mds-content-row container">

<?php if($pp->pageSidebar1): ?>
   <div class="mds-sidebar1 <?php echo $pp->classSidebar1; ?>"><?php echo $pp->pageSidebar1; ?></div> 
<?php endif; ?>

   <div class="mds-content <?php echo $pp->classContent; ?>"><?php echo $pp->pageContent; ?></div>

<?php if($pp->pageSidebar2): ?>
   <div class="mds-sidebar2 <?php echo $pp->classSidebar2; ?>"><?php echo $pp->pageSidebar2; ?></div> 
<?php endif; ?>

  </div>
 </div>
</div>


<!-- footer -->
<div id="mds-footer-wrap" class="hide">
 <footer id="mds-footer" class="container">

 </footer> 
</div>


<!-- bottom -->
<div id="mds-bottom-wrap">
 <footer id="mds-bottom" class="container">

  <p class="span-24 last"><em>This site is built using <a href="http://phpmedes.org/">the free and opensource software named phpmedes</a>. PHPMedes is a PHP framework and a CMS, based on PHP, that aids in quickly building, small and medium sized websites, using latest technique, with a minimum of knowledge needed. PHPMedes is free software, open source, and therefore licensed according to <a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License version 3</a>.</em></p>
	
  <?php echo $pp->GetHTMLForDeveloperMenu(); ?>

 </footer>
</div>

</body>
</html>