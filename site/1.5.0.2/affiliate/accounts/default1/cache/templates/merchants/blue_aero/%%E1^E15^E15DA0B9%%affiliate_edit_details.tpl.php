<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from affiliate_edit_details.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_edit_details.tpl', 6, false),)), $this); ?>
<!-- affiliate_edit_details -->

<div class="PersonalInfo">

<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Personal Info'), $this);?>
</legend>
	<?php echo "<div id=\"username\"></div>"; ?>
	<?php echo "<div id=\"rpassword\"></div>"; ?>
	<?php echo "<div id=\"firstname\"></div>"; ?>
	<?php echo "<div id=\"lastname\"></div>"; ?>
	<?php echo "<div id=\"refid\"></div>"; ?>
	<?php echo "<div id=\"rstatus\"></div>"; ?>
	<?php echo "<div id=\"parentuserid\"></div>"; ?>
	<?php echo "<div id=\"note\"></div>"; ?>
	<div class="AddAffiliatePhoto"><?php echo "<div id=\"photo\"></div>"; ?></div>
	<?php echo "<div id=\"dontSendEmail\"></div>"; ?>
	<?php echo "<div id=\"createSignupReferralComm\"></div>"; ?>
	<div class="clear"></div>
</fieldset>

<?php echo "<div id=\"DynamicFields1\"></div>"; ?>

<?php echo "<div id=\"DynamicFields2\"></div>"; ?>
<div class="clear"></div>
<?php echo "<div id=\"SaveButton\" class=\"PersonalSave\"></div>"; ?>
<div class="clear"></div>
</div>