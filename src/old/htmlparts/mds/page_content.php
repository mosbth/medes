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

