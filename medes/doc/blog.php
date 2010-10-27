<?php
require_once("../config.php");
$pp->pageTitle = "Blog";
//$pp->Dump();
include($pp->medesPath . "/inc/header.php");
?>


<article>
<h1>Blog</h1>
<p>The development blog, currently hardcoded but eventually replaced by the first phpmedes addon.

<h2>Start by tagging development release v0.1.0</h2>
<p class=bloggentry-subtitle>October 22, 2010 by Mikael Roos</p>

<p>So, now we are live. I needed to make this site work before sending out the latest newsletter to 
the db-o-webb-courses.

<p>After some initial discussions on the <a href="http://dbwebb.se/irc">irc-channel</a>, we are now up and running.
We need a stable version with the basic structure, before we can start with addon-development. So,
Rickard and I have to put this first version together. It will be a mix of code from htmlphp and with
some influences of phpersia. It must be a simple and easy system for those who do not know to much of PHP.
phpmedes is the simpler variant of phpersia. First phpmedes, then phpersia. That is the basic thought.
<p>So, lets code the first basic structure. Then the fun begins. Stay tuned and Enjoy.
</p>
</article>

<?php include($pp->medesPath . "/inc/footer.php"); ?>