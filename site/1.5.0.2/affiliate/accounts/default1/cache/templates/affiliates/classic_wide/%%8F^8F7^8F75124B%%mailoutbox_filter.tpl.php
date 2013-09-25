<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:24
         compiled from mailoutbox_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'mailoutbox_filter.tpl', 6, false),)), $this); ?>
<!-- mailoutbox_filter -->
    
    	<div class="MailOutBoxFilter">
    
       		<fieldset class="Filter">
            <legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"status\"></div>"; ?>
            </div>
       		</fieldset>
        
        	<fieldset class="Filter">
            <legend><?php echo smarty_function_localize(array('str' => 'Scheduled at'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"scheduled_at\"></div>"; ?>
            </div>
            </fieldset>
            
        </div>
            
        <div style="clear: both;"></div>
   