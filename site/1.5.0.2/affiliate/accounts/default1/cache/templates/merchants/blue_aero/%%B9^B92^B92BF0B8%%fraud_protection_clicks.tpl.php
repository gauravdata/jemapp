<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from fraud_protection_clicks.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'fraud_protection_clicks.tpl', 5, false),)), $this); ?>
<!-- fraud_protection_clicks -->

<div class="FraudProtection">
    <?php echo "<div id=\"bannedips_clicks_from_iframe\"></div>"; ?>
	<div class="Inliner"><?php echo "<div id=\"repeating_clicksInput\"></div>"; ?></div><div class="Inliner"><div class="Label"><?php echo smarty_function_localize(array('str' => 'Recognize multiple repeating clicks that come from the same IP address within'), $this);?>
</div></div>
	<div class="FormFieldSmallInline"><?php echo "<div id=\"repeating_clicks_seconds\"></div>"; ?></div> <div class="Inliner"><?php echo smarty_function_localize(array('str' => ' seconds.'), $this);?>
</div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
        <div class="Inliner FraudProtectionActionLabel"><?php echo smarty_function_localize(array('str' => 'What to do with these clicks'), $this);?>
</div><div class="Inliner"><?php echo "<div id=\"repeating_clicks_action\"></div>"; ?></div>
        <div class="ClearBoth"></div>
    </div>
    <div class="clear"></div>    
	<div class="Inliner"><?php echo "<div id=\"repeating_banner_clicksInput\"></div>"; ?></div><div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Click from same IP, but on different banners, don\'t recognize as repeating click'), $this);?>
</div>		
	
	<div class="Line" ></div>
	
	<div class="Inliner"><?php echo "<div id=\"bannedips_clicksInput\"></div>"; ?></div><div class="Inliner"><div class="Label"><?php echo smarty_function_localize(array('str' => 'Ban clicks from IP addresses'), $this);?>
</div></div>
	<div class="Inliner"><?php echo "<div id=\"bannedips_list_clicks\" class=\"BannedIps\"></div>"; ?></div> <div class="Inliner"></div>
	<div class="clear"></div>
	<div class="FraudProtectionAction">
		<div class="Inliner FraudProtectionActionLabel"><?php echo smarty_function_localize(array('str' => 'What to do with these clicks'), $this);?>
</div><div class="Inliner"><?php echo "<div id=\"bannedips_clicks_action\"></div>"; ?></div>
		<div class="ClearBoth"></div>
	</div>

	<div class="Line" ></div>
	
	<?php echo "<div id=\"FraudFeaturesPanel\"></div>"; ?>

</div>