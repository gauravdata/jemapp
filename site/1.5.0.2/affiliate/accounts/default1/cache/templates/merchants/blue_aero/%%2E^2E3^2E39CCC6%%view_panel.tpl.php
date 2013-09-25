<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:53
         compiled from view_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'view_panel.tpl', 5, false),)), $this); ?>
<!-- view_panel -->

<div class="MandatoryFields">
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Choose view'), $this);?>
 </legend>
    <?php echo "<div id=\"viewType\"></div>"; ?>
    <br />
    <?php echo "<div id=\"EditView\"></div>"; ?>
</fieldset>
</div>

<div class="ButtonSet"><?php echo "<div id=\"CancelButton\"></div>"; ?></div>