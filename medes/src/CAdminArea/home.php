<?php

// ------------------------------------------------------------------------------
//
// Create and echo the html using the available template
//
$page = <<<EOD
<h1>Admin Area</h1>
<p>Here you can change global settings of the site.</p>
EOD;

eval($template);
