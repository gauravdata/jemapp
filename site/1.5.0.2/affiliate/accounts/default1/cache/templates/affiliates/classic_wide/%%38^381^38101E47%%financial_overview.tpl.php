<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:14
         compiled from financial_overview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'financial_overview.tpl', 4, false),)), $this); ?>
<!-- financial_overview -->
<div class="OverviewDataBox">
	<div class="OverviewDataBoxContent">
    	<div class="OverviewHeader"><strong><?php echo smarty_function_localize(array('str' => 'FinancialOverview'), $this);?>
</strong></div>
	    <div class="OverviewInnerBox">
    <?php echo smarty_function_localize(array('str' => 'FinancialOverviewDescription'), $this);?>

    <br /><br />
	<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
 <strong><?php echo "<div id=\"approvedCommissions\"></div>"; ?></strong>  <?php echo smarty_function_localize(array('str' => 'approved unpaid commissions'), $this);?>

	<br />
	<?php echo smarty_function_localize(array('str' => 'and'), $this);?>
 <strong><?php echo "<div id=\"pendingCommissions\"></div>"; ?></strong>  <?php echo smarty_function_localize(array('str' => 'commissions waiting for approval by merchant'), $this);?>

		</div>
    </div>
</div>