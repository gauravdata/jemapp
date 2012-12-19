<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:10
         compiled from multiple_currencies_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'multiple_currencies_panel.tpl', 4, false),)), $this); ?>
<!--    multiple_currencies_panel   -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Multiple currencies'), $this);?>
</legend>
    <?php echo "<div id=\"grid\"></div>"; ?>
</fieldset>