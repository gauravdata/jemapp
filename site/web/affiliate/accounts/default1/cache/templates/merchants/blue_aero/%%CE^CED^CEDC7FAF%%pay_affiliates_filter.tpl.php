<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from pay_affiliates_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'pay_affiliates_filter.tpl', 6, false),)), $this); ?>
<!-- pay_affiliates_filter -->

			<div class="PayAffiliatesFilter">

				<fieldset class="Filter">			
				<legend><?php echo smarty_function_localize(array('str' => 'Show affiliates'), $this);?>
</legend>
				<div class="Resize">
				<?php echo "<div id=\"reachedMinPayout\"></div>"; ?>
				</div>
				</fieldset>

				<fieldset class="Filter">
				<legend><?php echo smarty_function_localize(array('str' => 'Payout method'), $this);?>
</legend>
				<div class="Resize">
 				<?php echo "<div id=\"payoutMethods\"></div>"; ?>
 				</div>
 				</fieldset>
 
				<fieldset class="Filter">
				<legend><?php echo smarty_function_localize(array('str' => 'Date created'), $this);?>
</legend>
				<div class="Resize">
				<?php echo "<div id=\"dateinserted\"></div>"; ?>
				</div>
				</fieldset>
				
				<fieldset class="Filter">
                <legend><?php echo smarty_function_localize(array('str' => 'Affiliate'), $this);?>
</legend>
                <div class="Resize">
                <?php echo "<div id=\"rstatus\"></div>"; ?>
                </div>
                </fieldset>
                
                <fieldset class="Filter FilterAffiliate">
                <legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
                <div class="Resize">
                <?php echo "<div id=\"campaignid\"></div>"; ?> 
                </div>
                </fieldset>
                
                <fieldset class="Filter">
                <legend><?php echo smarty_function_localize(array('str' => 'Transaction data'), $this);?>
</legend>
                <div class="Resize">
                <?php echo "<div id=\"transactionData\"></div>"; ?> 
                </div>
                </fieldset>

			</div>

			<div style="clear: both;"></div>
   