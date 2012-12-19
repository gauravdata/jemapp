<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:28
         compiled from compressed_commission_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'compressed_commission_settings.tpl', 7, false),)), $this); ?>
<!--	compressed_commission_settings		-->


<?php echo "<div id=\"PlacementOverviewGrid\"></div>"; ?>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'General settings'), $this);?>
</legend>
    <?php echo "<div id=\"processing\"></div>"; ?>
    <?php echo "<div id=\"recurrence\"></div>"; ?>
    <?php echo "<div id=\"recurrenceDay\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"ruleConditions\"></div>"; ?>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Action with transactions of affiliates, who didn\'t achieve conditions'), $this);?>
</legend>
    <?php echo "<div id=\"action\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"formmessage\"></div>"; ?>
<?php echo "<div id=\"sendButton\"></div>"; ?>
<?php echo "<div id=\"cancelButton\"></div>"; ?>