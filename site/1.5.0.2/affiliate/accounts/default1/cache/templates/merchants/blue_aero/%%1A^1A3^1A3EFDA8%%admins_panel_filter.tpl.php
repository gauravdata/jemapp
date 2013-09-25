<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from admins_panel_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'admins_panel_filter.tpl', 6, false),)), $this); ?>
<!--	admins_panel_filter		-->

    	<div class="UserFilter">
    	        
       		<fieldset class="Filter">
            <legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"rstatus\"></div>"; ?>
            </div>
        	</fieldset>
        
        </div>
        
        <div style="clear: both;"></div>