<?php
include('./header.php');
?>

<h2>HTML Signup form</h2>
<p>
PAP has a standard signup form <a href="../affiliates/signup.php">here</a>, but it allows you to make your own signup form in HTML and place it anywhere you want.
</p>

<table width="600" align="center">
<tr><td>

<fieldset>
<legend>HTML Signup form</legend>

<?php if(isset($_POST)) { ?>

<fieldset style="width: 400px; margin-left: 100px;">
    <legend>POST returned this data</legend>
<?php
    foreach($_POST as $k => $v) {
        echo "$k = $v<br>";

    }
?>
</fieldset>
<?php } ?>

<form action="../affiliates/signup.php" method="post">
<fieldset>
    <legend>Personal Info</legend>
    <table cellpadding="3">
      <tr><td width="150px"><strong>Username (Email)</strong></td><td><input type="text" name="username"></td></tr>
      <tr><td><strong>First name</strong></td><td><input type="text" name="firstname"></td></tr>
      <tr><td><strong>Last name</strong></td><td><input type="text" name="lastname"></td></tr>
      <tr><td>Referral ID</td><td><input type="text" name="refid"></td></tr>
    </table>
</fieldset>

<fieldset>
    <legend>Additional info</legend>
    <table cellpadding="3">
        <tr><td width="150px"><strong>Web Url</strong></td><td><input type="text" name="data1"></td></tr>
        <tr><td><strong>Company name</strong></td><td><input type="text" name="data2"></td></tr>
        <tr><td><strong>Street</strong></td><td><input type="text" name="data3"></td></tr>
        <tr><td><strong>City</strong></td><td><input type="text" name="data4"></td></tr>
        <tr><td><strong>State</strong></td><td><input type="text" name="data5"></td></tr>
        <tr><td><strong>Country</strong></td><td><input type="text" name="data6"></td></tr>
      </table>
</fieldset>

<fieldset style="text-align: center">
    <legend>Terms & conditions</legend>

    <textarea cols="50" rows="5">You can write your own terms & conditions here</textarea>
    <br />
    I confirm that I agree with terms & conditions <input type="checkbox" name="agreeWithTerms" value="Y">
      <br /><br />
    <?php if(array_key_exists('cumulativeErrorMessage', $_POST) && $_POST['cumulativeErrorMessage'] != '') { ?>
    <fieldset style="color: #ff0000;">
        <legend>There were errors</legend>
        <?php echo $_POST['cumulativeErrorMessage']?>
    </fieldset>
    <?php } ?>
    <br />
    
    <input type="submit" value="Signup">
    <input type="hidden" name="errorUrl" value="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>">
</fieldset>

</form>

</fieldset>
  </td>
</tr>
</table>


<fieldset>
  <legend>HTML source code for this signup form</legend>

<pre>
&lt;form action="http://www.yoursite.com/affiliates/signup.php" method="post"&gt;
&lt;fieldset&gt;
    &lt;legend&gt;Personal Info&lt;/legend&gt;
    &lt;table cellpadding="3"&gt;
      &lt;tr&gt;&lt;td width="150px"&gt;&lt;strong&gt;Username (Email)&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="username"&gt;&lt;/td&gt;&lt;/tr&gt;
      &lt;tr&gt;&lt;td&gt;&lt;strong&gt;First name&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="firstname"&gt;&lt;/td&gt;&lt;/tr&gt;
      &lt;tr&gt;&lt;td&gt;&lt;strong&gt;Last name&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="lastname"&gt;&lt;/td&gt;&lt;/tr&gt;
      &lt;tr&gt;&lt;td&gt;Referral ID&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="refid"&gt;&lt;/td&gt;&lt;/tr&gt;
    &lt;/table&gt;
    &lt;/fieldset&gt;

&lt;fieldset&gt;
    &lt;legend&gt;Additional info&lt;/legend&gt;
    &lt;table cellpadding="3"&gt;
        &lt;tr&gt;&lt;td width="150px"&gt;&lt;strong&gt;Web Url&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="data1"&gt;&lt;/td&gt;&lt;/tr&gt;
        &lt;tr&gt;&lt;td&gt;&lt;strong&gt;Company name&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="data2"&gt;&lt;/td&gt;&lt;/tr&gt;
        &lt;tr&gt;&lt;td&gt;&lt;strong&gt;Street&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="data3"&gt;&lt;/td&gt;&lt;/tr&gt;
        &lt;tr&gt;&lt;td&gt;&lt;strong&gt;City&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="data4"&gt;&lt;/td&gt;&lt;/tr&gt;
        &lt;tr&gt;&lt;td&gt;&lt;strong&gt;State&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="data5"&gt;&lt;/td&gt;&lt;/tr&gt;
        &lt;tr&gt;&lt;td&gt;&lt;strong&gt;Country&lt;/strong&gt;&lt;/td&gt;&lt;td&gt;&lt;input type="text" name="data6"&gt;&lt;/td&gt;&lt;/tr&gt;
    &lt;/table&gt;
    &lt;/fieldset&gt;

&lt;fieldset style="text-align: center"&gt;
    &lt;legend&gt;Terms & conditions&lt;/legend&gt;

    &lt;textarea cols="50" rows="5"&gt;You can write your own terms & conditions here&lt;/textarea&gt;
    &lt;br/&gt;
    I confirm that I agree with terms & conditions &lt;input type="checkbox" name="agreeWithTerms" value="Y"&gt;
    &lt;br/&gt;&lt;br/&gt;
    
    &lt;?php
      if(array_key_exists('cumulativeErrorMessage', $_POST) && $_POST['cumulativeErrorMessage'] != '') {
    ?&gt;
    &lt;fieldset style="color: #ff0000;"&gt;
        &lt;legend&gt;There were errors&lt;/legend&gt;
        &lt;?php echo $_POST['cumulativeErrorMessage']?&gt;
    &lt;/fieldset&gt;
    &lt;?php
      }
    ?&gt;
    &lt;br/&gt;
    
    &lt;input type="submit" value="Signup"&gt;
    &lt;input type="hidden" name="errorUrl" value="http://www.yoursite.com/html_signup_form.php"&gt;
    &lt;input type="hidden" name="successUrl" value="http://www.yoursite.com/after_signup.php"&gt;

&lt;/form&gt;
</pre>
</fieldset>

<h3>Documentation</h3>
<br />
The HTML signup form must be sent to /affiliates/signup.php after signup. Then there are two modes of operations.
If you don't include the <strong>errorUrl</strong>,<strong>successUrl</strong> hidden fields, the form will be processed by standard signup form.
<br />
If there are some errors, they will be displayed in the standard signup form.
<p>
<strong>If you want to be sent back to the HTML signup form in case of error</strong>, you have to add hidden field <strong>errorUrl</strong> to the form and this <strong>file have to be PHP!</strong>
Then the error messages and all values will be sent back by POST method to the URL you specified in <strong>errorUrl</strong> hidden field.
</p>


<table border="0" cellspacing="2" cellpadding="3">
<tr>
  <td class="header" colspan="3">Possible HTML form parameters (as hidden fields)</td>
</tr>

<tr><td class="label">errorUrl</td><td class="space">&nbsp;</td>
    <td>URL where the data should be sent in case of error. It should be URL address of the HTML signup form</td></tr>

<tr><td class="label">successUrl</td><td class="space">&nbsp;</td>
    <td>URL where the data should be sent when signup is successful. It will override any setting from merchant panel. It can be "thank you for signup" page.</td></tr>

<tr>
  <td class="header" colspan="3">Data returned to successUrl or errorUrl</td>
</tr>

<tr><td class="label">cumulativeErrorMessage</td><td class="space">&nbsp;</td>
    <td>all (optional) error messages grouped together in on string</td></tr>

<tr><td class="label">successMessage</td><td class="space">&nbsp;</td>
    <td>success message</td></tr>

<tr><td class="label">list of fields</td><td class="space">&nbsp;</td>
    <td>
    list of signup form fields returned with the value sent and (optional) error message.
    <br />
    Example:<br />
    username - contains value entered by user<br />
    usernameError - contains error message for this field<br />
    firstname - contains value entered by user<br />
    firstnameError - contains error message for this field<br />
    </td></tr>

</table>

<?php
include('./footer.php');
?>
