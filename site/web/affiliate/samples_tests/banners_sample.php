<?php
include('./header.php');
?>

<h2>Sample banners and links</h2>
<p>
see various banners in action below, and test clicks tracking by clicking on them
</p>

<fieldset>
<legend>Image banner</legend>

<a href="./sample_homepage.php?a_aid=testaff&a_bid=11110001&chan=mychannel2">
<img src="../accounts/default1/banners/sample_image_banner.gif" alt="" title="" WIDTH="468" HEIGHT="60"></a>
<img src='../scripts/imp.php?a_aid=testaff&a_bid=11110001' width='1' height='1' border='0'>

</fieldset>

<br/>
<fieldset>
<legend>Text link</legend>

<a href="./sample_homepage.php?a_aid=testaff&a_bid=11110002">
<strong>Click here</strong><br>to find out more about this link</a>
<img src='../scripts/imp.php?a_aid=testaff&a_bid=11110002' width='1' height='1' border='0'>

</fieldset>


<br/>
<fieldset>
<legend>Flash banner</legend>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="468" HEIGHT="60">
<PARAM NAME="movie" VALUE="../accounts/default1/banners/sample_flash_banner.swf?clickTAG=http%3A%2F%2Fwww.qualityunit.com%3Fa_aid%3Dtestaff%26a_bid%3D11110003">
<PARAM NAME="loop" VALUE=""><PARAM NAME="menu" VALUE="false"><PARAM NAME="quality" VALUE="medium"><PARAM NAME="wmode" VALUE="Opaque">
<EMBED src="../accounts/default1/banners/sample_flash_banner.swf?clickTAG=http%3A%2F%2Fwww.qualityunit.com%3Fa_aid%3Dtestaff%26a_bid%3D11110003" loop="" menu="false" swLiveConnect="FALSE" wmode="Opaque" WIDTH="468" HEIGHT="60" TYPE="application/x-shockwave-flash">
</OBJECT>

</fieldset>


<br/>
<fieldset>
<legend>HTML banner</legend>

<table width="100%" border=0 cellpadding=3>
	<tr>
		<td align=left valign=top><img
			src="../accounts/default1/banners/sample_html_banner_image.gif"></td>
		<td></td>
		<td align=left valign=top><b>Post Affiliate Pro</b><br>

		- a powerful affiliate management system that allows you to:<br>
		- easy set up and maintain your own affiliate program <br>
		- pay your affiliates per lead per click per sale or %commission.<br>
		- multi-tier commissions: up to 10 tiers<br>
		- get more traffic for you website without additional costs<br>
		- increase sales<br>
		- already used by more than thousand merchants worldwide <BR>
		</td>
	</tr>
	<tr>
		<td colspan=3 align=left>Post Affiliate Pro offers you a vast
		spectrum of features and PRICE / FEATURES RATIO IS THE BEST you can
		find. <BR>
		You also get FREE INSTALLATION, lifetime upgrades and fast and helpful
		support.<br>
		<br>
		<b>Post Affiliate Pro</b> is compatible with nearly all merchant
		accounts, payment gateways, shopping carts and membership systems. <br />
		<a style="color: red;"
			href="./sample_homepage.php?a_aid=testaff&a_bid=11110004">Click
		here to learn more</a></td>
	</tr>
</table>
</fieldset>

<?php
include('./footer.php');
?>
