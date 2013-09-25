<html>
<body>

<form action="safepay.php" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="custom5" value=""><br/>
Amount: <input type="text" name="iamount" value="120.50"><br/>
Transaction ID: <input type="text" name=tid value="12345678-59422"><br/>

<input type="hidden" name="itemNum" value="2">
<input type="hidden" name="result" value="1">
<input type="hidden" name="passPhrase" value="<?php echo strtoupper(md5('abc')) ?>">
<input type="hidden" name="ipayer" value="abnc123">
<input type="hidden" name="_ipn_act" value="_ipn_payment">
<input type="hidden" name="confirmID" value="abnc123">
<input type="hidden" name="iquantity" value="2">

<input type="hidden" name="PDebug" value="y">
<input type="submit" value="Test normal sale">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>
</form>  

<hr>
<form action="safepay.php" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="custom" value=""><br/>
Amount: <input type="text" name="mc_gross" value="120.50"><br/>
Transaction ID: <input type="text" name=subscr_id value="SUB_12345"><br/>

<input type="hidden" name="item_number" value="2">
<input type="hidden" name="payment_status" value="Completed">
<input type="hidden" name="txn_type" value="subscr_payment">

<input type="hidden" name="PDebug" value="y">
<input type="submit" value="Test recurring payment / subscription">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>
</form> 
<hr>
<form action="safepay.php" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="custom" value=""><br/>
Amount: <input type="text" name="mc_gross" value="-20"><br/>
Transaction ID: <input type="text" name=txn_id value="AB_12345"><br/>
Parent transaction ID: <input type="text" name=parent_txn_id value="ORD_123"><br/>
<input type="hidden" name="item_number" value="2">
<input type="hidden" name="reason_code" value="refund">
<input type="hidden" name="payment_status" value="Completed">
<input type="hidden" name="txn_type" value="web_accept">

<input type="hidden" name="PDebug" value="y">
<input type="submit" value="Test refund">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>
</form>   
 
</body>
</html>
