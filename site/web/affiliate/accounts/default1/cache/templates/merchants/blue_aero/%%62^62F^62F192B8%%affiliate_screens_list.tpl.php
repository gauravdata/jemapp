<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from affiliate_screens_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_screens_list.tpl', 2, false),)), $this); ?>
<!-- affiliate_screens_list -->
<?php echo smarty_function_localize(array('str' => 'Here you can see list of all screens available in merchant panel'), $this);?>

<?php echo "<div id=\"AddCustomPageButton\"></div>"; ?>
<?php echo "<div id=\"AddUrlPageButton\"></div>"; ?>
<div class="clear"></div>
<br/>
<h3><?php echo smarty_function_localize(array('str' => 'Custom pages'), $this);?>
</h3>
<?php echo "<div id=\"CustomScreens\"></div>"; ?>
<br/><br/>
<h3><?php echo smarty_function_localize(array('str' => 'Predefined pages'), $this);?>
</h3>
<?php echo "<div id=\"PredefinedScreens\"></div>"; ?>