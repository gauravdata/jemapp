<?php /* Smarty version 2.6.18, created on 2011-08-17 23:00:51
         compiled from signup_fields.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'signup_fields.tpl', 4, false),)), $this); ?>
<!-- signup_fields -->
<div class="SignupForm">
    <fieldset>
        <legend><?php echo smarty_function_localize(array('str' => 'Personal Info'), $this);?>
</legend>
        <?php echo "<div id=\"username\"></div>"; ?>
        <?php echo "<div id=\"firstname\"></div>"; ?>
        <?php echo "<div id=\"lastname\"></div>"; ?>
        <?php echo "<div id=\"refid\"></div>"; ?>
        <?php echo "<div id=\"parentuserid\"></div>"; ?>
    </fieldset>
    
    <fieldset>
        <legend><?php echo smarty_function_localize(array('str' => 'Additional info'), $this);?>
</legend>
        <?php echo "<div id=\"data1\"></div>"; ?>
        <?php echo "<div id=\"data2\"></div>"; ?>        <?php echo "<div id=\"data3\"></div>"; ?>        <?php echo "<div id=\"data4\"></div>"; ?>        <?php echo "<div id=\"data5\"></div>"; ?>        <?php echo "<div id=\"data6\"></div>"; ?>        <?php echo "<div id=\"data7\"></div>"; ?>
        <?php echo "<div id=\"data8\"></div>"; ?>
        <?php echo "<div id=\"data9\"></div>"; ?>
        <?php echo "<div id=\"data10\"></div>"; ?>
        <?php echo "<div id=\"data11\"></div>"; ?>
        <?php echo "<div id=\"data12\"></div>"; ?>
        <?php echo "<div id=\"data13\"></div>"; ?>
        <?php echo "<div id=\"data14\"></div>"; ?>
        <?php echo "<div id=\"data15\"></div>"; ?>
        <?php echo "<div id=\"data16\"></div>"; ?>
        <?php echo "<div id=\"data17\"></div>"; ?>
        <?php echo "<div id=\"data18\"></div>"; ?>
        <?php echo "<div id=\"data19\"></div>"; ?>
        <?php echo "<div id=\"data20\"></div>"; ?>
        <?php echo "<div id=\"data21\"></div>"; ?>
        <?php echo "<div id=\"data22\"></div>"; ?>
        <?php echo "<div id=\"data23\"></div>"; ?>
        <?php echo "<div id=\"data24\"></div>"; ?>
        <?php echo "<div id=\"data25\"></div>"; ?>
        <?php echo "<div id=\"notificationemail\"></div>"; ?>
        <?php echo "<div id=\"recaptcha\"></div>"; ?>
    </fieldset>
    
    <?php echo "<div id=\"payoutMethods\"></div>"; ?>
    <?php echo "<div id=\"termsAndConditions\" class=\"TermsAndConditions\"></div>"; ?>
    <?php echo "<div id=\"agreeWithTerms\"></div>"; ?>
    <?php echo "<div id=\"FormMessage\"></div>"; ?>
    <?php echo "<div id=\"SignupButton\"></div>"; ?>
</div>