<html>
<body>
--------------------------------normal payment------------------------------------------
<form action="worldpay.php" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="M_aid" value=""><br/>
Amount: <input type="text" name="amount" value="120.50"><br/>
Transaction ID: <input type="text" name=transId value="AB_12345"><br/>
Product ID: <input type="text" name="M_ProductID" value="XYZ_ABC_123"><br/>
<input type="hidden" name="transStatus" value="Y">
<input type="hidden" name="desc" value="bla bla bala">
<input type="hidden" name="futurePayId" value="123456">
<input type="hidden" name="PDebug" value="y">
<input type="submit" value="Submit">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>        
</form> 
-------------------------------init recur. payment----------------------------------------
<form action="worldpay.php" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="M_aid" value=""><br/>
Amount: <input type="text" name="amount" value="120.50"><br/>
Transaction ID: <input type="text" name=transId value="AB_12345"><br/>
Product ID: <input type="text" name="M_ProductID" value="XYZ_ABC_123"><br/>
<input type="hidden" name="transStatus" value="Y">
<input type="hidden" name="desc" value="dhsf sdf afks ask jhakg jhasd jk 123456 cdsjk fskjfhsjkdhfskj">
<input type="hidden" name="futurePayId" value="789456">
<input type="hidden" name="PDebug" value="y">
<input type="submit" value="Submit">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>        
</form> 
-------------------------------continue recur. payment----------------------------------------
<form action="worldpay.php" method=post>
Amount: <input type="text" name="amount" value="120.50"><br/>
Transaction ID: <input type="text" name=transId value="AB_12345"><br/>
Product ID: <input type="text" name="M_ProductID" value="XYZ_ABC_123"><br/>
<input type="hidden" name="transStatus" value="Y">
<input type="hidden" name="desc" value="dhsf sdf afks ask jhakg jhasd jk f FuturePay agreement ID 789456 cdsjk fskjfhsjkdhfskj">
<input type="hidden" name="futurePayId" value="789456">
<input type="hidden" name="PDebug" value="y">
<input type="submit" value="Submit">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>        
</form> 
       
</body>
</html>
