<?php
include('./header.php');
?>

<h2>Banners with channels and SubId tracking</h2>
<p>
PAP allows your affiliates to track their advertising channels. 
This means that they can create for example channel for AdWords, and put their affiliate link on adwords.
The same link can be put to a promotion email, with a different channel code.
<br/>
Another example would be creating a channel for different places on your site.
<br/>
PAP will track not only the user referral, but also through which channel he came. This way your affiliate will know which channel (banner placing) converts most.
</p>

<p>
The good thing about channels and Sub ID codes is that <strong>they are passed also to sales / leads</strong>.
So your affiliate will know not only which referral (click) was made through which channel, but also which sales were made through the channel. 
</p>

<p>
Note that the banner codes used in these sample pages are specially changed to work in the test conditions.
You can see how this banner will look in the real situation in the banner code example.  
</p>

<br/>
<fieldset>
<legend>Channel code example</legend>

<a href="./sample_homepage.php?a_aid=testaff&a_bid=11110001&chan=testchnl">
<img src="../accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"></a>
<img src='../scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'>

<p>
<strong>Banner code (example)</strong>
<pre>
&lt;a href="http://www.targetsite.com/?a_aid=testaff&a_bid=11110001<strong>&chnl=testchnl</strong>"&gt;
&lt;img src="http://www.yoursite.com/affiliate/accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"&gt;&lt;/a&gt;
&lt;img src='http://www.yoursite.com/affiliate/scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'&gt;
</pre>
</p>

</fieldset>


<h3>SubId tracking</h3>
<p>
Except channel variable you can use two more custom variables: <strong>data1</strong>, <strong>data2</strong>.
They work exactly like channels, and can be used for additional tracking.<br/>
</p>

<br/>
<fieldset>
<legend>SubId data examples</legend>

<a href="./sample_homepage.php?a_aid=testaff&a_bid=11110001&data1=somedata&data2=even_more_data">
<img src="../accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"></a>
<img src='../scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'>

<p>
<strong>Banner code (example)</strong>
<pre>
&lt;a href="http://www.targetsite.com/?a_aid=testaff&a_bid=11110001<strong>&data1=somedata&data2=even_more_data</strong>"&gt;
&lt;img src="http://www.yoursite.com/affiliate/accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"&gt;&lt;/a&gt;
&lt;img src='http://www.yoursite.com/affiliate/scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'&gt;
</pre>
</p>
</fieldset>
			   
<?php
include('./footer.php');
?>
