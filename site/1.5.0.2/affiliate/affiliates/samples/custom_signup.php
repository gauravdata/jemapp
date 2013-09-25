<?php
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
?>
<form action="http://127.0.0.1/PostAffiliatePro/trunk/server/affiliates/signup.php" method="post"><!-- signup_fields -->
<div class="SignupForm">
    <fieldset>
        <legend>Personal Info</legend>
<div id="usernameLabel"></div>
<div id="usernameInput"></div>
        <label><strong>Username (Email)</strong><input type="text" name="username" value="<?php echo $_POST['username'] ?>"></label>
        <label><strong>First name</strong><input type="text" name="firstname" value="<?php echo $_POST['firstname'] ?>"></label>
        <label><strong>Last name</strong><input type="text" name="lastname" value="<?php echo $_POST['lastname'] ?>"></label>
        <label>Referral ID</strong><input type="text" name="refid" value="<?php echo $_POST['refid'] ?>"></label>
    </fieldset>

    <label><strong>Web Url</strong><input type="text" name="data1" value="<?php echo $_POST['data1'] ?>"></label>
    <label><strong>Company name</strong><input type="text" name="data2" value="<?php echo $_POST['data2'] ?>"></label>
    <label><strong>Street</strong><input type="text" name="data3" value="<?php echo $_POST['data3'] ?>"></label>
    <label><strong>City</strong><input type="text" name="data4" value="<?php echo $_POST['data4'] ?>"></label>
    <label><strong>State</strong><input type="text" name="data5" value="<?php echo $_POST['data5'] ?>"></label>
    <label><strong>Country</strong><input type="text" name="data6" value="<?php echo $_POST['data6'] ?>"></label>

    <fieldset>
        <label>Terms & conditions</label><textarea>terms and conditions</textarea>
        <label>I agree with terms & conditions</strong>
               <input type="checkbox" name="agreeWithTerms" value="Y" <?php if ($_POST['agreeWithTerms'] == "Y") echo 'checked="checked"'; ?>></label>
    </fieldset>
    
    <div id="FormMessage"></div>
    <input type="hidden" name="errorUrl" value="samples/custom_signup.php?success=false">
    <input type="hidden" name="successUrl" value="samples/custom_signup.php?success=true">
    <input type="submit" value="Signup">
</div>
</form>
<?php
  if ($_GET['success'] == 'true') {
      echo '<font color="green">' . $_POST['successMessage'] . '</font>';
  } else if ($_GET['success'] == 'false') {
      echo '<font color="red">' . $_POST['cumulativeErrorMessage'] . '</font>';  
  }
?>
<hr>
<b>List of all variables posted to this script:</b><br>
<?php
foreach ($_POST as $name => $value) {
    echo $name . " = " .$value . "<br>";
}
?>
