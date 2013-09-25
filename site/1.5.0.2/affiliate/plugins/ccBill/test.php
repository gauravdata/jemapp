<html>
<body>

<form action="ccbill.php?PDebug=Y" method=post>
User (cookie): <input id="pap_dx8vc2s5" type="text" name="PAP_COOKIE" value=""><br/>
Totalcost: <input type="text" name="initialPrice" value="120.50"><br/>
Transaction ID: <input type="text" name=subscription_id value="AB_12345"><br/>
ProductID: <input type="text" name=ProductID value="PR_12345"><br/>

Customer first name: <input type="text" name=customer_fname value="fname1"><br/>
Customer last name: <input type="text" name=customer_lname value="lname1"><br/>
Customer email: <input type="text" name=email value="user@name.com"><br/>
Customer city: <input type="text" name=city value="User City"><br/>
Customer address: <input type="text" name=address1 value="User Address"><br/>

<input type="hidden" name="PDebug" value="Y">
<input type="submit" value="Test normal sale">
<script id="pap_x2s6df8d" src="../../scripts/notifysale.php" type="text/javascript">
</script>
</form>  
 
</body>
</html>
