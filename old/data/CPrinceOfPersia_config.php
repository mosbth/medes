a:12:{s:7:"siteurl";s:7:"/medes/";s:18:"htmlparts-htmlhead";s:659:"<?php echo $pp->GetHTMLDocType(); ?>
<head>
	<!-- Meta tags -->
	<?php echo $pp->GetHTMLForMeta(); ?>

	<!-- Use PHP to set the page title dynamic -->
 	<?php echo "<title>$pp->pageTitle</title>"; ?>
 	
	<!-- Stylesheets and style -->
	<?php echo $pp->GetHTMLForStyle(); ?>

	<!-- Script -->
	<?php echo $pp->GetHTMLForScript(); ?>

	<!-- Help Internet Explorer understand HTML5 elements http://code.google.com/p/html5shiv/ -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Support Google Analytics -->
	<?php if(!empty($pp->googleAnalytics)) echo $pp->googleAnalytics; ?>

</head>
<body>
";s:17:"htmlparts-pagetop";s:200:"<div id="mds-top-wrap" class="clearfix">
 <div id="mds-top-left"><?php echo $pp->GetHTMLForRelatedSitesMenu(); ?></div>
 <div id="mds-top-right"><?php echo $pp->GetHTMLForLoginMenu(); ?></div>
</div>
";s:20:"htmlparts-pageheader";s:459:"<div id="mds-header-wrap">
 <div id="mds-header" class="container">

  <div id="mds-header-logo">
   <a href="<?php echo $pp->PrependWithSiteUrl('medes/page/template.php') ?>"><img src="<?php echo $pp->PrependWithSiteUrl('img/logo_medes_335x70.png'); ?>" alt="Logo" width="335" height="70" style="margin-left:0px;"/></a>
  </div>

  <div id="mds-header-title"></div>

  <div id="mds-header-menu"><?php echo $pp->GetHTMLForMainMenu(); ?></div>

 </div>
</div>
";s:21:"htmlparts-pagecontent";s:577:"<div id="mds-content-wrapper" class="prepend-top append-bottom">

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

";s:20:"htmlparts-pagefooter";s:104:"<div id="mds-footer-wrap" class="hide">
 <footer id="mds-footer" class="container">

 </footer> 
</div>
";s:20:"htmlparts-pagebottom";s:622:"<div id="mds-bottom-wrap">
 <footer id="mds-bottom" class="container">

  <p class="span-24 last">This site is built using <a href="http://phpmedes.org/">the free and opensource software named phpmedes</a>. PHPMedes is a PHP framework and a CMS, based on PHP, that aids in quickly building, small and medium sized websites, using latest technique, with a minimum of knowledge needed. PHPMedes is free software, open source, and therefore licensed according to <a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License version 3</a>.</p>
	
  <?php echo $pp->GetHTMLForDeveloperMenu(); ?>

 </footer>
</div> ";s:10:"navigation";a:2:{s:6:"navbar";a:2:{s:4:"text";s:19:"Main navigation bar";s:3:"nav";a:6:{i:1;a:3:{s:4:"text";s:4:"home";s:3:"url";s:23:"medes/page/template.php";s:5:"title";s:37:"A default template page to start with";}i:2;a:3:{s:4:"text";s:4:"page";s:3:"url";s:35:"medes/page/page.php?p=template-page";s:5:"title";s:51:"A template page that stores content in the database";}i:3;a:3:{s:4:"text";s:7:"columns";s:3:"url";s:28:"medes/page/column_layout.php";s:5:"title";s:53:"Example page to show how flexible column layout works";}i:4;a:3:{s:4:"text";s:3:"acp";s:3:"url";s:18:"medes/page/acp.php";s:5:"title";s:50:"Administrate and configure the site and its addons";}i:5;a:3:{s:4:"text";s:3:"ucp";s:3:"url";s:18:"medes/page/ucp.php";s:5:"title";s:18:"User control panel";}i:6;a:3:{s:4:"text";s:7:"install";s:3:"url";s:22:"medes/page/install.php";s:5:"title";s:7:"Install";}}}s:12:"relatedsites";a:2:{s:4:"text";s:13:"Top left menu";s:3:"nav";a:2:{i:1;a:3:{s:4:"text";s:8:"phpmedes";s:3:"url";s:20:"http://phpmedes.org/";s:5:"title";s:16:"Home of phpmedes";}i:2;a:3:{s:4:"text";s:6:"dbwebb";s:3:"url";s:17:"http://dbwebb.se/";s:5:"title";s:58:"Databases and Webb, it´s all about html, css, php and sql";}}}}s:10:"styletheme";a:4:{s:4:"name";s:4:"core";s:10:"stylesheet";s:24:"screen_compatibility.css";s:5:"print";s:9:"print.css";s:2:"ie";s:6:"ie.css";}s:4:"meta";a:4:{s:6:"author";s:0:"";s:9:"copyright";s:0:"";s:11:"description";s:0:"";s:8:"keywords";s:0:"";}s:7:"tracker";s:5:"moped";s:8:"password";a:3:{s:8:"function";s:4:"sha1";s:9:"timestamp";s:32:"fb62117a168453935dc9e1cb5b56b4de";s:8:"password";s:40:"60eab3552cd57a7e141fe1d9b2ce4a1378cf5c3d";}}