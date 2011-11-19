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

<!-- top -->
<div id="mds-top-wrap" class="clearfix">
 <div id="mds-top-left"><?php echo $pp->GetHTMLForMenu('relatedsites'); ?></div>
 <div id="mds-top-right"><?php echo $pp->GetHTMLForMenu('login'); ?></div>
</div>


<!-- header -->
<div id="mds-header-wrap">
 <div id="mds-header-area" class="container">
  <div id="mds-header"><?php echo $pp->RenderViewsForRegion('header'); ?></div>
  <div id="mds-header-logo"><?php echo $pp->GetHTMLForLogo(); ?></div>
  <div id="mds-header-title"><?php echo $pp->GetHTMLMessage('sitetitle'); ?></div>
  <div id="mds-header-menu"><?php echo $pp->GetHTMLForMenu('main'); ?></div>
 </div>
</div>


<!-- promoted -->
<div id="mds-promoted-wrap">
 <div id="mds-promoted-area" class="container prepend-top append-bottom">
  <div id="mds-promoted" class="span-24 last"><?php echo $pp->RenderViewsForRegion('promoted'); ?></div>
 </div>
</div>


<!-- content -->
<div id="mds-main-wrap">
 <div id="mds-main-area" class="container prepend-top append-bottom">

 <?php core_CalculateContentWidth($hasSidebar1, $hasSidebar2, $classContent, $classSidebar1, $classSidebar2); ?>
 <?php if($hasSidebar1): ?>
  <div id="mds-sidebar1" class="<?php echo $classSidebar1; ?>"><?php echo $pp->RenderViewsForRegion('sidebar1'); ?></div> 
 <?php endif; ?>

  <div id="mds-content" class="<?php echo $classContent; ?>"><?php echo $pp->RenderViewsForRegion('content'); ?></div>

 <?php if($hasSidebar2): ?>
  <div id="mds-sidebar2" class="<?php echo $classSidebar2; ?>"><?php echo $pp->RenderViewsForRegion('sidebar2'); ?></div> 
 <?php endif; ?>

 </div>
</div>


<!-- triptych -->
<div id="mds-triptych-wrap">
 <div id="mds-triptych-area" class="container prepend-top append-bottom">
  <div id="mds-triptych1" class="span-8"><?php echo $pp->RenderViewsForRegion('triptych1'); ?></div>
  <div id="mds-triptych2" class="span-8"><?php echo $pp->RenderViewsForRegion('triptych2'); ?></div>
  <div id="mds-triptych3" class="span-8 last"><?php echo $pp->RenderViewsForRegion('triptych3'); ?></div>
 </div> 
</div>


<!-- footer -->
<div id="mds-footer-wrap">
 <footer id="mds-footer-area" class="container">
  <div id="mds-footercol-area" class="prepend-top append-bottom">
   <div id="mds-footercol1" class="span-6"><?php echo $pp->RenderViewsForRegion('footercol1'); ?></div>
   <div id="mds-footercol2" class="span-6"><?php echo $pp->RenderViewsForRegion('footercol2'); ?></div>
   <div id="mds-footercol3" class="span-6"><?php echo $pp->RenderViewsForRegion('footercol3'); ?></div>
   <div id="mds-footercol4" class="span-6 last"><?php echo $pp->RenderViewsForRegion('footercol4'); ?></div>
  </div>
  <div id="mds-footer" class="append-bottom span-24 last"><?php echo $pp->RenderViewsForRegion('footer'), $pp->GetHTMLMessage('footer'), $pp->GetHTMLMessage('copyright'), $pp->GetHTMLForDeveloper(); ?></div>   
 </footer> 
</div>


</body>
</html>