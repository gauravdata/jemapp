<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:46
         compiled from transaction_list_filter_base.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'transaction_list_filter_base.tpl', 6, false),)), $this); ?>
<!-- transaction_list_filter_base -->

			<div class="TransactionsFilter">
				<div class="ColumnFieldset">
					<fieldset class="Filter FilterChannel">
	                    <legend><?php echo smarty_function_localize(array('str' => 'Channel'), $this);?>
</legend>
	                    <div class="Resize">
	                    <?php echo "<div id=\"channelValue\"></div>"; ?>
	                    </div>
	   				</fieldset>    
	    
	  				<fieldset class="Filter FilterCampaign">
	    			<legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
	    			<div class="Resize">
	    			<?php echo "<div id=\"campaignid\"></div>"; ?>
	    			</div>
	    			</fieldset>
	    
	    		</div>
				 <fieldset class="Filter FilterDate">
                        <legend><?php echo smarty_function_localize(array('str' => 'Date created'), $this);?>
</legend>
                        <div class="Resize">
                            <?php echo "<div id=\"dateinserted\"></div>"; ?>
                        </div>
                </fieldset>   
            
           	
            	<div class="ColumnFieldset">
			
					<fieldset class="Filter">
					<legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
					<div class="Resize">
            		<?php echo "<div id=\"rstatus\"></div>"; ?>
            		</div>
            		</fieldset>
            
            		<fieldset class="Filter">
					<legend><?php echo smarty_function_localize(array('str' => 'Type'), $this);?>
</legend>
					<div class="Resize">
            		<?php echo "<div id=\"rtype\"></div>"; ?>
            		</div>
            		</fieldset>
            
            	</div>

            	<div class="ColumnFieldset">
	            	<fieldset class="Filter"> 
	            		<legend><?php echo smarty_function_localize(array('str' => 'Order ID'), $this);?>
</legend>
	            		<div class="Resize">
	            		<?php echo "<div id=\"orderId\"></div>"; ?>
	            		</div>
	            		</fieldset>
            	
	            	<fieldset class="Filter">
	            	<legend><?php echo smarty_function_localize(array('str' => 'Custom'), $this);?>
</legend>
	            	<div class="Resize">
	            	<?php echo "<div id=\"custom\"></div>"; ?>
	            	</div>
	            	</fieldset>
            	</div>
            </div>
            
            <div style="clear: both;"></div>