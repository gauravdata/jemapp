<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:32
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
        <?php echo "<div id=\"refid\" class=\"Refid\"></div>"; ?>
        <?php echo "<div id=\"parentuserid\"></div>"; ?>            
    </fieldset>
    
    <fieldset>
        <legend><?php echo smarty_function_localize(array('str' => 'Additional info'), $this);?>
</legend>
        <?php echo "<div id=\"data1\"></div>"; ?>        <?php echo "<div id=\"data2\"></div>"; ?>        <?php echo "<div id=\"data3\"></div>"; ?>        <?php echo "<div id=\"data4\"></div>"; ?>        <?php echo "<div id=\"data5\"></div>"; ?>        <?php echo "<div id=\"data6\"></div>"; ?>    </fieldset>
    
    <?php echo "<div id=\"payoutMethods\"></div>"; ?>
    <?php echo "<div id=\"termsAndConditions\" class=\"TermsAndConditions\"></div>"; ?>
    <?php echo "<div id=\"agreeWithTerms\"></div>"; ?>
    <?php echo "<div id=\"FormMessage\"></div>"; ?>
    <?php echo "<div id=\"SignupButton\"></div>"; ?>
</div>