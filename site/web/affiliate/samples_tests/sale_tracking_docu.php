<?php
include('./header.php');
?>

<h2>Sale / lead / action tracking parameters</h2>
<p>
in your sale tracking code you can use various parameters that wil pass additional data about the transaction.
</p>

<strong>All possible tracking parameters</strong>
<p>
<pre>
&lt;script id="pap_x2s6df8d" src="http://www.yoursite.com/affiliate/scripts/salejs.php" type="text/javascript"&gt;
&lt;/script&gt;
&lt;script type="text/javascript"&gt;
var sale = PostAffTracker.createSale();
sale.setTotalCost('120.50');
sale.setFixedCost('20.50');
sale.setOrderID('ORD_12345XYZ');
sale.setProductID('test product');
sale.setAffiliateID('testaff');
sale.setCampaignID('11111111');
sale.setChannelID('chan');
sale.setCoupon('CouponCode');
sale.setCustomCommission('10.23');
sale.setCurrency('EUR');
sale.setStatus('A');
sale.setData1('something');
sale.setData2('something');
sale.setData3('something');
sale.setData4('something');
sale.setData5('something');

PostAffTracker.setVisitorId('ae5f51c3145771c87a0fe467000FvW6e');

PostAffTracker.writeCookieToCustomField('id_field');
PostAffTracker.writeAffiliateToCustomField('id_field');
PostAffTracker.writeCookieToLink('id_field', 'papCookie');
PostAffTracker.writeAffiliateToLink('id_field', 'a_aid');

PostAffTracker.register();
&lt;/script&gt;
</pre>

<br />
<table border="0" cellspacing="2" cellpadding="3">
<tr>
  <td class="header" colspan="3">Sale / lead / action parameters</td>
</tr>

<tr><td class="label">setTotalCost()</td><td class="space">&nbsp;</td>
    <td>total cost of the order. It is required for percentage commissions campaigns, otherwise optional</td></tr>

<tr><td class="label">setFixedCost()</td><td class="space">&nbsp;</td>
    <td>fixed cost of the order. Fixed cost is substracted from totalcost before commission is computed. If you put % in front of the number, the fixed cost will be computed as percentage</td></tr>

<tr><td class="label">setOrderID()</td><td class="space">&nbsp;</td>
    <td>ID of the order. Can be used for recognizing duplicate transactions</td></tr>

<tr><td class="label">setProductID()</td><td class="space">&nbsp;</td>
    <td>ID of the product</td></tr>

<tr><td class="label">setAffiliateID()</td><td class="space">&nbsp;</td>
    <td>ID or referral ID of the affiliate. With this parameter you can force to register commission to this affiliate</td></tr>

<tr><td class="label">setCampaignID()</td><td class="space">&nbsp;</td>
    <td>ID of the campaign. With this parameter you can force to register commission using this campaign</td></tr>

<tr><td class="label">setChannelID()</td><td class="space">&nbsp;</td>
    <td>ID of the channel. With this parameter you can force to register commission for this channel</td></tr>

<tr><td class="label">setCoupon()</td><td class="space">&nbsp;</td>
    <td>Coupon code. If set, affiliate is recognized from coupon code</td></tr>

<tr><td class="label">setCustomCommission()</td><td class="space">&nbsp;</td>
    <td>value of custom commissions. You can force to use this commissions value instead of commissions set in campaign. If you put % in front of the number, the commission will be computed as percentage</td></tr>

<tr><td class="label">setCurrency()</td><td class="space">&nbsp;</td>
    <td>currency code. You can force to use this currency instead of default currency. (you need to have Multiple currencies feature enabled)</td></tr>

<tr><td class="label">setStatus()</td><td class="space">&nbsp;</td>
    <td>force to set this status for this commission. You can use these states:
        <ul>
          <li>'A' - approved</li>
          <li>'P' - pending</li>
          <li>'D' - declined</li>
        </ul>
    </td></tr>

<tr><td class="label">setData1()</td><td class="space">&nbsp;</td>
    <td>set custom data for this transaction. You have up to five fields.</td></tr>

<tr><td class="label">setData2()</td><td class="space">&nbsp;</td>
    <td>set additional custom data for this transaction</td></tr>

<tr><td class="label">setData3()</td><td class="space">&nbsp;</td>
    <td>set additional custom data for this transaction</td></tr>

<tr><td class="label">setData4()</td><td class="space">&nbsp;</td>
    <td>set additional custom data for this transaction</td></tr>

<tr><td class="label">setData5()</td><td class="space">&nbsp;</td>
    <td>set additional custom data for this transaction</td></tr>

<tr><td colspan="3">&nbsp;</td></tr>
<tr>
    <td class="header" colspan="3">Global tracker parameters</td>
</tr>

<tr><td class="label">PostAffTracker.setVisitorId()</td><td class="space">&nbsp;</td>
    <td>custom cookie value. With this parameter you can force to register commission with this cookie value.<br />
    The cookie value stands for visitor ID which is stored in PAP4 database representing the relation between visitor and the referring affiliate.</td></tr>

<tr><td colspan="3">&nbsp;</td></tr>
<tr>
    <td class="header" colspan="3">Helper tracker functions</td>
</tr>

<tr>
  <td class="label">PostAffTracker.writeCookieToCustomField()</td>
  <td class="space">&nbsp;</td>
  <td>this function writes the value of the cookie into input field with the specified ID</td>
</tr>
<tr>
  <td class="label">PostAffTracker.writeAffiliateToCustomField()</td>
  <td class="space">&nbsp;</td>
  <td>this function writes the affiliate ID value from the cookie into input field with the specified ID</td>
</tr>
<tr>
  <td class="label">PostAffTracker.writeCookieToLink()</td>
  <td class="space">&nbsp;</td>
  <td>this function appends the value of the cookie into the specified link</td>
</tr>
<tr>
  <td class="label">PostAffTracker.writeAffiliateToLink()</td>
  <td class="space">&nbsp;</td>
  <td>this function appends the affiliate ID value into the specified link</td>
</tr>

<tr><td class="label">PostAffTracker.register()</td><td class="space">&nbsp;</td>
    <td>this function will call the affiliate system and saves the commission. This function MUST BE CALLED if you want to save the commissions.</td></tr>

</table>
</p>

<h3>Examples of use</h3>

<br />
<fieldset>
<legend>Saving commission with no additional parameters passed</legend>

It will save commission with no other parameters. Makes sense only for fixed (not percentage) commissions. Also, you have to enable "saving commission for zero total cost".

<pre>
&lt;script id="pap_x2s6df8d" src="http://www.yoursite.com/affiliate/scripts/salejs.php" type="text/javascript"&gt;
&lt;/script&gt;
&lt;script type="text/javascript"&gt;
var sale = PostAffTracker.createSale();
PostAffTracker.register();
&lt;/script&gt;
</pre>
</fieldset>

<br />
<fieldset>
<legend>Saving per action commission</legend>

It will save per action commission with code 'signup'.

<pre>
&lt;script id="pap_x2s6df8d" src="http://www.yoursite.com/affiliate/scripts/salejs.php" type="text/javascript"&gt;
&lt;/script&gt;
&lt;script type="text/javascript"&gt;
var signup = PostAffTracker.createAction('signup');
signup.setOrderID('ORD_12345XYZ');
signup.setProductID('Product1');
PostAffTracker.register();
&lt;/script&gt;
</pre>
</fieldset>


<br />
<fieldset>
<legend>Saving multiple commissions with one call</legend>

You can use this code if you want to save commissions separately for different items in your shopping cart.

<pre>
&lt;script id="pap_x2s6df8d" src="http://www.yoursite.com/affiliate/scripts/salejs.php" type="text/javascript"&gt;
&lt;/script&gt;
&lt;script type="text/javascript"&gt;
var sale = PostAffTracker.createSale();
sale.setTotalCost('50.50');
sale.setOrderID('ORD_12345XYZ');
sale.setProductID('Product1');

var sale2 = PostAffTracker.createSale();
sale2.setTotalCost('35.40');
sale2.setOrderID('ORD_12345XYZ');
sale2.setProductID('Product2');

var sale3 = PostAffTracker.createSale();
sale3.setTotalCost('67.30');
sale3.setOrderID('ORD_12345XYZ');
sale3.setProductID('Product3');

PostAffTracker.register();
&lt;/script&gt;
</pre>
</fieldset>

<br />
<fieldset>
<legend>Saving commission to a specific affiliate</legend>

This will save a commission to the affiliate with ID or Referrer ID = testaff, doesn't matter who really referred the sale / lead.
<pre>
&lt;script id="pap_x2s6df8d" src="http://www.yoursite.com/affiliate/scripts/salejs.php" type="text/javascript"&gt;
&lt;/script&gt;
&lt;script type="text/javascript"&gt;
var sale = PostAffTracker.createSale();
sale.setTotalCost('120.50');
sale.setOrderID('ORD_12345XYZ');
sale.setProductID('test product');
sale.setAffiliateID('testaff');

PostAffTracker.register();
&lt;/script&gt;
</pre>
</fieldset>


<br />
<fieldset>
<legend>Displaying value of the cookie and affiliate</legend>

You can use this functionality to get the ID of affiliate who referred your customer, and then use it in your own script.<br />
Note that this is callback function, and getting the value is delayed 1 second.
It is because the JavaScript code has to get the cookie also from the Flash object.

<pre>
&lt;script id="pap_x2s6df8d" src="http://www.yoursite.com/affiliate/scripts/salejs.php" type="text/javascript"&gt;
&lt;/script&gt;
&lt;script type="text/javascript"&gt;
PostAffTracker.writeCookieToCustomField('id_field');
PostAffTracker.writeAffiliateToCustomField('id_field');
PostAffTracker.writeCookieToLink('id_field', 'papCookie');
PostAffTracker.writeAffiliateToLink('id_field', 'a_aid');
&lt;/script&gt;
</pre>

Full cookie: <input type="text" name="full_cookie_info" value="" id="full_cookie_info" size="30" readonly><br />

Affiliate ID: <input type="text" name="full_cookie_info" value="" id="aff_cookie_info" size="30" readonly><br />


Link with added cookie info: <a href="http://www.yoursite.com/payment.php?amount=120&product=P1&order=123" id="affCookieLinkId">see destination url of this link</a><br />

Link with added affiliate id: <a href="http://www.yoursite.com/payment.php?amount=120&product=P1&order=123" id="affLinkId">see destination url of this link</a>

<script id="pap_x2s6df8d" src="../scripts/salejs.php" type="text/javascript">
</script>
<script type="text/javascript">
PostAffTracker.writeCookieToCustomField('full_cookie_info');
PostAffTracker.writeAffiliateToCustomField('aff_cookie_info');
PostAffTracker.writeCookieToLink('affCookieLinkId', 'papCookie');
PostAffTracker.writeAffiliateToLink('affLinkId', 'a_aid');
</script>
           
<?php
include('./footer.php');
?>
