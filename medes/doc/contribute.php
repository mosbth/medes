<?php
require_once("../config.php");
$pp->pageTitle = "Contribute";
//$pp->Dump();
include($pp->medesPath . "/inc/header.php");
?>

<article>
<h1>Contribute</h1>

<h2>Help will be needed</h2>
<p>You may contribute by helping the opensource project that develops phpmedes. You may assist in
many ways, for example:

<ul>
<li>Code the kernel or addons
<li>Use phpmedes to build websites and provide feedback
<li>Inspect code, test
<li>Develop stylesheets
<li>Write documentation
</ul>

<h2>Talk with the development team</h2>
<p>Join in at out irc-channel at <a href="http://dbwebb.se/irc">http://dbwebb.se/irc</a> and meet us there.

<p>Send email to Mikael Roos as mos@bth.se.

<h2>Learn the basics of software engineering</h2>

<p>Join the htmlphp-course (Databases, HTML, CSS and scriptbased PHP-programming).

<p>Prepare to help by learning how to use git as a version control system. Read the following articles:

<ul>
<li><a href="http://book.git-scm.com/3_basic_branching_and_merging.html ">http://book.git-scm.com/3_basic_branching_and_merging.html</a>
<li><a href="http://gitster.livejournal.com/30313.html">Git development in team with remote branches</a>
</ul>

<!--
Att rätta buggar i tidigare relase.

    * git checkout -b 5.02 v5.02 # Create a new branch from the tag v5.02
    * git push origin 5.02 # push the branch to github
    * Make changes in new branch and tag it to v5.03
    * commit and push 
-->

<p>Read the article on how to become a hacker at <a href="http://www.catb.org/esr/faqs/hacker-howto.html">http://www.catb.org/esr/faqs/hacker-howto.html</a>
to get the right atitude.

<p>Learn how to use irssi and screen.

<p>Learn unix.

<h2>Licenses and Copyright</h2>
<p><a href="http://drupal.org/licensing/faq/">drupal.org has a good page on licenses and copyright</a>, same principles applies to phpmedes.

</p>
</article>

<?php include($pp->medesPath . "/inc/footer.php"); ?>