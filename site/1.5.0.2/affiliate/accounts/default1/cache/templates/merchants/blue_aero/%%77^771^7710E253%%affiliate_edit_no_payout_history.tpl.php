<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from affiliate_edit_no_payout_history.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_edit_no_payout_history.tpl', 2, false),)), $this); ?>
<!-- affiliate_edit_no_payout_history -->
<div class="noDataHeader"><?php echo smarty_function_localize(array('str' => 'There were no payouts for this affiliate so far'), $this);?>
</div>
<div class="noDataText"><?php echo smarty_function_localize(array('str' => 'Once a payment for this affiliate is issued. It will appear here'), $this);?>
</div>
<div class="affEditNoPayoutHistNoData">&nbsp;</div>