<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from account_signup_settings_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'account_signup_settings_form.tpl', 6, false),)), $this); ?>
<!--	account_signup_settings_form	-->

<?php echo "<div id=\"SignupFormUrl\"></div>"; ?>
<div class="clear"></div>
<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Account approval'), $this);?>
</legend>
	<?php echo "<div id=\"account_approval\"></div>"; ?>
</fieldset>

<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'After signup'), $this);?>
</legend>
	<?php echo "<div id=\"account_post_signup_type\" class=\"SignUrl\"></div>"; ?>	
</fieldset>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Default Merchant Agreement'), $this);?>
</legend>
    <div class="AccountDetailsAgreement">
    <?php echo "<div id=\"forceMerchantAgreementAcceptance\"></div>"; ?>
    <?php echo "<div id=\"merchant_agreement\"></div>"; ?>
    </div>  
</fieldset>

<?php echo "<div id=\"formMessage\"></div>"; ?>
<?php echo "<div id=\"saveButton\"></div>"; ?>