<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from invoices_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'invoices_filter.tpl', 5, false),)), $this); ?>
<!-- invoices_filter -->

<div class="LicensesFilter">
    <fieldset class="Filter FilterCustom">
    <legend><?php echo smarty_function_localize(array('str' => 'Custom'), $this);?>
</legend>
    <div class="Resize">
    <?php echo "<div id=\"custom\"></div>"; ?>
    </div>
    </fieldset>
</div>
        
<div style="clear: both;"></div>