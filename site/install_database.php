<?php

// Use Medes bootstrap to gain access to fully populated $pp
define('MEDES_FRONTCONTROLLER_PASS', true);
define('MEDES_TEMPLATEENGINE_PASS', true);
include(__DIR__ . "/../index.php");

// For all classes, check if module IInstallable, call method for install
echo "hej";

