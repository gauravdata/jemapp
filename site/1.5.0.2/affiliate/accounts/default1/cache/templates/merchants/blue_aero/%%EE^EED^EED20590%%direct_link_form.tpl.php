<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:38
         compiled from direct_link_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'direct_link_form.tpl', 11, false),)), $this); ?>
<!-- direct_link_form -->
<fieldset>
<?php echo "<div id=\"url\"></div>"; ?>
<?php echo "<div id=\"userid\"></div>"; ?>
<?php echo "<div id=\"channelid\"></div>"; ?>
<?php echo "<div id=\"note\"></div>"; ?>
<?php echo "<div id=\"rstatus\"></div>"; ?>
</fieldset>
<fieldset>
<?php echo "<div id=\"campaignid\"></div>"; ?>
<?php echo smarty_function_localize(array('str' => 'or'), $this);?>

<?php echo "<div id=\"bannerid\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>