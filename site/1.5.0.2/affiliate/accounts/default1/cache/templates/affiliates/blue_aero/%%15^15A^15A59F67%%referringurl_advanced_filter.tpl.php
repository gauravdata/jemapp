<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:32
         compiled from referringurl_advanced_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'referringurl_advanced_filter.tpl', 6, false),)), $this); ?>
<!-- referringurl_advanced_filter -->

			<div class="ReferringURLFilter">
			
		    <fieldset class="Filter">       
            <legend><?php echo smarty_function_localize(array('str' => 'Date of sale'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"datetime\"></div>"; ?>
            </div>
       		</fieldset>
        
        	<fieldset class="Filter">
            <legend><?php echo smarty_function_localize(array('str' => 'Sale status'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"rstatus\"></div>"; ?>
            </div>
       		</fieldset>
        
        	<fieldset class="Filter">
            <legend><?php echo smarty_function_localize(array('str' => 'Group by'), $this);?>
</legend>
            <div class="Resize">
            <?php echo "<div id=\"groupby\"></div>"; ?>
            </div>
       		</fieldset>	
        
       		</div>
        
      		<div style="clear: both;"></div>