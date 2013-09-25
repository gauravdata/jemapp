<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from affiliate_logins_map.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_logins_map.tpl', 3, false),)), $this); ?>
<!-- affiliate_logins_map -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Logins Map'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Map shows last 20 unique IP addresses from which affiliate logged in'), $this);?>

<?php echo "<div id=\"Map\"></div>"; ?>
</fieldset>