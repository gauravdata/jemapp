<?php
include('./header.php');
?>

<h2>Sample sale / lead tracking</h2>
<p>
this page simulates your order confirmation or "thank you for order" page. 
It contains hidden sale tracking code that notifies the affiliate system about the sale. 
</p>


<h3>Sales / leads tracking explained</h3>
<p>
To track leads and sales, you have to use sale tracking code.
The exact integration depends on your shopping cart or payment gateway, so refer to our documentation for this.
</p>

<strong>General tracking method</strong>
<p>
General tracking method uses javascript that you should put to your order confirmation page.
The general tracking code is:

<pre>
&lt;script id="pap_x2s6df8d" src="http://www.yoursite.com/affiliate/scripts/salejs.php" type="text/javascript"&gt;
&lt;/script&gt;
&lt;script type="text/javascript"&gt;
var sale = PostAffTracker.createSale();
sale.setTotalCost('120.50');
sale.setOrderID('ORD_12345XYZ');
sale.setProductID('test product');
PostAffTracker.register();
&lt;/script&gt;
</pre>
</p>

<strong>Hidden image exmaple</strong>
<p>
If you don't want to use JavaScript tracking code, you can use also hidden image (hidden pixel tracking) version.<br/>
Note that by using hidden the system cannot use functionality of Flash cookies, it will depend only on standard cookies and IP address. 

<br/><br/>
The hidden image variant of the tracking code above is:

<pre>
&lt;img src="http://www.yoursite.com/affiliate/scripts/sale.php?TotalCost=120.50&OrderID=ORD_12345XYZ&ProductID=test+product" width="1" height="1""&gt;
</pre>

<br/>
Variables you can use in hidden image are:<br/>
TotalCost, OrderID, ProductID, data1, data2, data3, data4, data5, AffiliateID, CampaignID, ChannelID, Commission, PStatus and Currency
</p>

<script id="pap_x2s6df8d" src="../scripts/salejs.php" type="text/javascript">
</script>
<script type="text/javascript">
var sale = PostAffTracker.createSale();
sale.setTotalCost('150.50');
sale.setOrderID('ORD_123');
sale.setFixedCost('$44');
sale.setProductID('test product');
PostAffTracker.register();
</script>
			   
<?php
include('./footer.php');
?>
