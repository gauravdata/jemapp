<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:31
         compiled from payout_fields_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'payout_fields_panel.tpl', 3, false),)), $this); ?>
<!--    payout_fields_panel     -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Payout method and data'), $this);?>
</legend>
    <?php echo "<div id=\"payoutoptionid\"></div>"; ?>
    <?php echo "<div id=\"payoutOptions\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Payout balances'), $this);?>
</legend>
<?php echo "<div id=\"minimumPayoutOptions\"></div>"; ?>
<?php echo "<div id=\"minimumpayout\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"applyVatInvoicing\"></div>"; ?>