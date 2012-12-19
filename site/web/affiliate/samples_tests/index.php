<?php
include('./header.php');
?>

<h2>Samples & tests</h2>
<h3>Note</h3>
<p>
This directory (/samples_tests) contains examples and tests to walk you through the various possibilities of the affiliate system.
<br/>
When you are ready to go online with your affiliate program, you can delete or rename this directory.
</p>

<fieldset>
<legend>Banner examples</legend>
<p><a href="./banners_sample.php">Banners sample</a><br/>
you can see all banner types in action and you can test clicks
</p>

<p><a href="./banners_linking.php">Banners with different link types</a><br/>
the banners below use different affiliate link types to demonstrate this functionality.
</p>

<p><a href="./banners_subid.php">Banners with channels and SubId tracking</a><br/>
sample how your affiliates can track advertisibng channels and use subId tracking
</p>
</fieldset>

<br/>
<fieldset>
<legend>Referral (click) tracking example</legend>
<p><a href="./sample_homepage.php">Sample homepage</a><br/>
the page that is the target of the banners and simulates your home page.<br/>
It contains referral (click) tracking code.
</p>

<p><a href="./cookie_info.php">Cookie information</a><br/>
Find out if the referral cookies were registered for this domain.
</p>
</fieldset>

<br/>
<fieldset>
<legend>Sale / lead tracking examples</legend>

<p><a href="./sale_tracking_normal.php">Normal sale tracking using general tracking method</a><br/>
page with active sale tracking code</p>

<p><a href="./sale_tracking_docu.php">Sale tracking with another optional parameters</a><br/>
description of all parameters you can pass to the sale tracking script</p>
</fieldset>

<br/>
<fieldset>
<legend>Signup form examples</legend>

<p><a href="./signup_html.php">HTML Signup form</a><br/>
Except normal signup form PAP allows you to create pure HTML signup form and place it to any page.
Here is an example of such signup form.</p>

</fieldset>

<?php
include('./footer.php');
?>
