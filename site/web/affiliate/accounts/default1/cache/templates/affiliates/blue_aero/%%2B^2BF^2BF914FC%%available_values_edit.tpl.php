<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:51
         compiled from available_values_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'available_values_edit.tpl', 4, false),)), $this); ?>
<!-- available_values_edit -->
<div style="background:white; border:1px solid blue;">
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Edit available values'), $this);?>
</legend>
    <?php echo "<div id=\"availableValues\"></div>"; ?>
    <?php echo smarty_function_localize(array('str' => 'Values are newline separated'), $this);?>
 
    <?php echo "<div id=\"closeButton\" class=\"EditListBoxCloseButton\"></div>"; ?>
</fieldset>
</div>