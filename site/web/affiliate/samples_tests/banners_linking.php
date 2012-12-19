<?php
include('./header.php');
?>

<h2>Banners with different link types</h2>
<p>
the banner below use different affiliate linking types to demonstrate this functionality.
<br/><br/>
Note that the banner codes used in these sample pages are specially changed to work in the test conditions.
You can see how this banner will look in the real situation in the banner code example.  
</p>

<br/>
<fieldset>
<legend>Standard links (redirect)</legend>

<a href="../scripts/click.php?a_aid=testaff&a_bid=11110001">
<img src="../accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"></a>
<img src='../scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'>

<p>
<strong>Banner code (example)</strong>
<pre>
&lt;a href="<strong>http://www.yoursite.com/affiliate/scripts/click.php?a_aid=testaff&a_bid=11110001</strong>"&gt;
&lt;img src="http://www.yoursite.com/affiliate/accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"&gt;&lt;/a&gt;
&lt;img src='http://www.yoursite.com/affiliate/scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'&gt;
</pre>
</p>

</fieldset>

<br/>
<fieldset>
<legend>New style links (URL parameters)</legend>

<a href="./sample_homepage.php?a_aid=testaff&a_bid=11110001">
<img src="../accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"></a>
<img src='../scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'>

<p>
<strong>Banner code (example)</strong>
<pre>
&lt;a href="<strong>http://www.targetsite.com/?a_aid=testaff&a_bid=11110001</strong>"&gt;
&lt;img src="http://www.yoursite.com/affiliate/accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"&gt;&lt;/a&gt;
&lt;img src='http://www.yoursite.com/affiliate/scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'&gt;
</pre>
</p>
</fieldset>

<br/>
<fieldset>
<legend>SEO links (require mod_rewrite)</legend>

<a href="./reftestaff/11110001.html">
<img src="../accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"></a>
<img src='../scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'>

<p>
<strong>Banner code (example)</strong>
<pre>
&lt;a href="<strong>http://www.targetsite.com/reftestaff/11110001.html</strong>"&gt;
&lt;img src="http://www.yoursite.com/affiliate/accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"&gt;&lt;/a&gt;
&lt;img src='http://www.yoursite.com/affiliate/scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'&gt;
</pre>
</p>
</fieldset>

<br/>
<fieldset>
<legend>DirectLink style (no URL parameters)</legend>

<a href="./sample_homepage.php">
<img src="../accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"></a>
<img src='../scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'>

<p>
<strong>Banner code (example)</strong>
<pre>
&lt;a href="<strong>http://www.targetsite.com/</strong>"&gt;
&lt;img src="http://www.yoursite.com/affiliate/accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"&gt;&lt;/a&gt;
&lt;img src='http://www.yoursite.com/affiliate/scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'&gt;
</pre>
</p>
</fieldset>
			   
<?php
include('./footer.php');
?>
