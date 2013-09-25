<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:46
         compiled from vat_fields_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'vat_fields_panel.tpl', 3, false),)), $this); ?>
<!--    vat_fields_panel    -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Invoicing options'), $this);?>
</legend>
    <?php echo "<div id=\"vatPercentage\"></div>"; ?>
    <?php echo "<div id=\"vatNumber\"></div>"; ?>
    <?php echo "<div id=\"amountOfRegCapital\"></div>"; ?>
    <?php echo "<div id=\"regNumber\"></div>"; ?>
</fieldset>