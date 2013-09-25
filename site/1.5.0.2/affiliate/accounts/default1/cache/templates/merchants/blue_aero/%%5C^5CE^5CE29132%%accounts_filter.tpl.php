<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from accounts_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'accounts_filter.tpl', 4, false),)), $this); ?>
<!--	accounts_filter		-->

<fieldset class="Filter">
    <legend><?php echo smarty_function_localize(array('str' => 'Account ballance till'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"datetime\"></div>"; ?>
    </div>
</fieldset>

<fieldset class="Filter">
	<legend><?php echo smarty_function_localize(array('str' => 'Account status'), $this);?>
</legend>
    <div class="Resize">
    	<?php echo "<div id=\"rstatus\"></div>"; ?>
	</div>
</fieldset>

<fieldset class="Filter">
    <legend><?php echo smarty_function_localize(array('str' => 'Custom filter'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"custom\"></div>"; ?>
    </div>
</fieldset>

<fieldset class="Filter">
    <legend><?php echo smarty_function_localize(array('str' => 'Statistics date range'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"statsdaterange\"></div>"; ?>
    </div>
</fieldset>

<div style="clear: both;"></div>