<html>
<body>

<form action="ejunkie.php?PDebug=Y" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="custom" value=""><br/>
Amount: <input type="text" name="mc_gross" value="120.50"><br/>
Transaction ID: <input type="text" name=txn_id value="AB_12345"><br/>

Customer first name: <input type="text" name=first_name value="fname1"><br/>
Customer last name: <input type="text" name=last_name value="lname1"><br/>
Customer email: <input type="text" name=payer_email value="user@name.com"><br/>
Customer city: <input type="text" name=address_city value="User City"><br/>
Customer address: <input type="text" name=address_street value="User Address"><br/>
Shipping: <input type="text" name=mc_shipping value=""><br/>
Tax: <input type="text" name=tax value=""><br/>

<input type="hidden" name="item_number" value="2">
<input type="hidden" name="payment_status" value="Completed">
<input type="hidden" name="txn_type" value="payment">
<input type="hidden" name="mc_currency" value="EUR">

<input type="hidden" name="PDebug" value="Y">
<input type="submit" value="Test normal sale">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>
</form>

<hr>

<form action="ejunkie.php?PDebug=Y" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="custom" value=""><br/>
Amount 1: <input type="text" name="mc_gross_1" value="120.50"><br/>
Amount 2: <input type="text" name="mc_gross_2" value="210.50"><br/>
Transaction ID: <input type="text" name=txn_id value="AB_12345"><br/>

Customer first name: <input type="text" name=first_name value="fname1"><br/>
Customer last name: <input type="text" name=last_name value="lname1"><br/>
Customer email: <input type="text" name=payer_email value="user@name.com"><br/>
Customer city: <input type="text" name=address_city value="User City"><br/>
Customer address: <input type="text" name=address_street value="User Address"><br/>
Shipping 1: <input type="text" name=mc_shipping1 value=""><br/>
Shipping 2: <input type="text" name=mc_shipping2 value=""><br/>
Tax: <input type="text" name=tax value=""><br/>
Tax 1: <input type="text" name=tax1 value=""><br/>
Tax 2: <input type="text" name=tax2 value=""><br/>

<input type="hidden" name="item_number1" value="2">
<input type="hidden" name="item_number2" value="3">
<input type="hidden" name="num_cart_items" value="2"/>
<input type="hidden" name="payment_status" value="Completed">
<input type="hidden" name="txn_type" value="payment">
<input type="hidden" name="mc_currency" value="EUR">

<input type="hidden" name="PDebug" value="Y">
<input type="submit" value="Test cart sale">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>
</form>

<hr>
<form action="ejunkie.php?PDebug=Y" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="custom" value=""><br/>
Amount: <input type="text" name="mc_gross" value="120.50"><br/>
Transaction ID: <input type="text" name=subscr_id value="SUB_12345"><br/>

Customer first name: <input type="text" name=first_name value="fname1"><br/>
Customer last name: <input type="text" name=last_name value="lname1"><br/>
Customer email: <input type="text" name=payer_email value="user@name.com"><br/>
Customer city: <input type="text" name=address_city value="User City"><br/>
Customer address: <input type="text" name=address_street value="User Address"><br/>

<input type="hidden" name="item_number" value="2">
<input type="hidden" name="payment_status" value="Completed">
<input type="hidden" name="txn_type" value="subscr_payment">

<input type="hidden" name="PDebug" value="y">
<input type="submit" value="Test recurring payment / subscription">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>
</form> 
<hr>
<form action="ejunkie.php?PDebug=Y" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="custom" value=""><br/>
Transaction ID: <input type="text" name=txn_id value="AB_12345"><br/>
Parent transaction ID: <input type="text" name=parent_txn_id value="ORD_123"><br/>
<input type="hidden" name="item_number" value="2">
<input type="hidden" name="reason_code" value="refund">
<input type="hidden" name="payment_status" value="Refunded">
<input type="hidden" name="txn_type" value="web_accept">

<input type="hidden" name="PDebug" value="y">
<input type="submit" value="Test refund">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>
</form>   
 
</body>
</html>
