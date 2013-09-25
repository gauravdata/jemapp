<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from affiliate_logins.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_logins.tpl', 2, false),)), $this); ?>
<!-- affiliate_logins -->
<?php echo smarty_function_localize(array('str' => 'Following logins history will help you to identify possible fraudulent affiliates. Suspicious could be, if affiliate logs in with multiple IP addresses coming from different countries in short time.'), $this);?>
<br/>
<?php echo "<div id=\"LoginsMap\"></div>"; ?>
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Logins History'), $this);?>
</legend>
<?php echo "<div id=\"AffiliateLoginsGrid\"></div>"; ?>
</fieldset>