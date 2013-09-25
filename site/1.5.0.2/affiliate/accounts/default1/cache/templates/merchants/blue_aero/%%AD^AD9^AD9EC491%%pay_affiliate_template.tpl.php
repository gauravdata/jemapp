<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from pay_affiliate_template.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'pay_affiliate_template.tpl', 3, false),)), $this); ?>
<!-- pay_affiliate_template -->
<?php echo "<div id=\"payAffiliateData\"></div>"; ?>
<?php echo smarty_function_localize(array('str' => 'Merchant note'), $this);?>
<br/>
<?php echo "<div id=\"paymentNote\"></div>"; ?><br/>
<?php echo smarty_function_localize(array('str' => 'Affiliate payment note'), $this);?>
<br/>
<?php echo "<div id=\"affiliateNote\"></div>"; ?><br/>
<?php echo "<div id=\"sendEmail\"></div>"; ?>
<?php echo smarty_function_localize(array('str' => 'Send email to affiliate about this payment'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'Email template'), $this);?>
<br/>
<?php echo "<div id=\"emailTemplate\"></div>"; ?><br/>

<?php echo "<div id=\"payButton\"></div>"; ?>