<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:53
         compiled from visitor_affiliates_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'visitor_affiliates_filter.tpl', 6, false),)), $this); ?>
<!-- visitor_affiliates_filter -->
    
    	<div class="VisitorAffiliatesFilter">
       		
       		<fieldset class="Filter">
            <legend><?php echo smarty_function_localize(array('str' => 'Date of visit'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"datevisit\"></div>"; ?>
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
        
        <div style="clear: both;"></div>