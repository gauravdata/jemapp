<?php
include('./header.php');
?>

<h2>Sample sale / lead tracking</h2>
<p>
this page simulates your order confirmation or "thank you for order" page. 
</p>


<h3>Cookies information</h3>
<p>
If somebody from this computer clicked on affiliate link before, the tracking cookie should be registered.

<fieldset>
<legend>Full cookie</legend>

Value: <input type="text" name="full_cookie_info" value="" id="fullCookieInfoId" size="30" readonly>

<br/><br/>
full cookie value has format AFFILIATEID_CAMPAIGNID_CHANNEL - the channel part is optional.
</fieldset>

<fieldset>
<legend>Affiliate ID part of the cookie</legend>
Value: <input type="text" name="aff_cookie_info" value="" id="affCookieInfoId" readonly>

<br/><br/>
this is the first part of the cookie above, and it contains ID of affiliate in our system.
</fieldset>
</p>

<fieldset>
<legend>Link with cookie information</legend>

Link with added cookie info:
<a href="http://www.yoursite.com/payment.php?amount=120&product=P1&order=123" id="affCookieLinkId">see destination url of this link</a>

<br/><br/>
</fieldset>

<fieldset>
<legend>Link with affiliate id</legend>

Link with added affiliate id:
<a href="http://www.yoursite.com/payment.php?amount=120&product=P1&order=123" id="affLinkId">see destination url of this link</a>

<br/><br/>
</fieldset>


<input type="button" onClick="history.go(0)" value="Refresh">

<script id="pap_x2s6df8d" src="../scripts/salejs.php" type="text/javascript">
</script>
<script type="text/javascript">
PostAffTracker.writeCookieToCustomField('fullCookieInfoId');
PostAffTracker.writeAffiliateToCustomField('affCookieInfoId');
PostAffTracker.writeCookieToLink('affCookieLinkId', 'papCookie');
PostAffTracker.writeAffiliateToLink('affLinkId', 'a_aid');
</script>
			   
<?php
include('./footer.php');
?>
