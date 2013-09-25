<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from fraud_protection_signups.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'fraud_protection_signups.tpl', 4, false),)), $this); ?>
<!-- fraud_protection_signups -->

<div class="FraudProtection">
	<div class="Inliner"><?php echo "<div id=\"repeating_signupsInput\"></div>"; ?></div><div class="Inliner"><div class="Label"><?php echo smarty_function_localize(array('str' => 'Recognize multiple signups that come from the same IP address within'), $this);?>
</div></div>
	<div class="FormFieldSmallInline"><?php echo "<div id=\"repeating_signups_seconds\"></div>"; ?></div> <div class="Inliner"><?php echo smarty_function_localize(array('str' => ' seconds.'), $this);?>
</div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel"><?php echo smarty_function_localize(array('str' => 'What to do with these signups'), $this);?>
</div><div class="Inliner"><?php echo "<div id=\"repeating_signups_actionInput\"></div>"; ?></div>
		<div class="ClearBoth"></div>
	</div>
		
	<div class="Line" ></div>
	
	<div class="Inliner"><?php echo "<div id=\"bannedips_signupsInput\"></div>"; ?></div><div class="Inliner"><div class="Label"><?php echo smarty_function_localize(array('str' => 'Don\'t allow signups from following IP addresses'), $this);?>
</div></div>
	<div class="Inliner"><?php echo "<div id=\"bannedips_list_signups\" class=\"BannedIps\"></div>"; ?></div> <div class="Inliner"></div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel"><?php echo smarty_function_localize(array('str' => 'What to do with these signups'), $this);?>
</div><div class="Inliner"><?php echo "<div id=\"bannedips_signups_actionInput\"></div>"; ?></div>
		<div class="ClearBoth"></div>
	</div>

	<div class="Line" ></div>
	
	<?php echo "<div id=\"FraudFeaturesPanel\"></div>"; ?>
</div>