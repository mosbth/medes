<?php
if(isset($_POST['doSaveArticle'])){
  if(isset($_POST['id']))
    self::Save();
  else
    self::SaveNew();
}
if(isset($_GET['id']))
  $page = self::Edit();
else
  $page = self::ListAll();
