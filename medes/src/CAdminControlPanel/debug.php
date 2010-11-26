<?php

// ------------------------------------------------------------------------------
//
// Get debug and config information and prepeare to print out.
//
$dumpPrinceOfPersia = CPrinceOfPersia::GetInstance()->Dump();
$server = print_r($_SERVER, true);
$session = print_r($_SESSION, true);

// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Debug</h1>
<p>Print out debug and configuration information.</p>
<h2>CPrinceOfPersia::Dump()</h2>
{$dumpPrinceOfPersia}
<h2>\$_SERVER</h2>
<pre>{$server}</pre>
<h2>\$_SESSION</h2>
<pre>{$session}</pre>

EOD;

