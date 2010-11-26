<?php
// self explanatory really
if(isset($_GET['id']))
  self::Delete();
header('location: ?p=edit');
