<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:10
         compiled from log_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'log_filter.tpl', 6, false),)), $this); ?>
<!-- log_filter -->
    
    	<div class="LogFilter">
       		
       		<fieldset class="Filter">
            <legend><?php echo smarty_function_localize(array('str' => 'Date created'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"created\"></div>"; ?>
            </div>
       		</fieldset>
        
        	<fieldset class="Filter">
            <legend><?php echo smarty_function_localize(array('str' => 'Time'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"time_created\"></div>"; ?><?php echo "<div id=\"time\"></div>"; ?>
            </div>
        	</fieldset>
        
        	<fieldset class="Filter">        	
            <legend><?php echo smarty_function_localize(array('str' => 'Log level'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"level\"></div>"; ?>
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
    