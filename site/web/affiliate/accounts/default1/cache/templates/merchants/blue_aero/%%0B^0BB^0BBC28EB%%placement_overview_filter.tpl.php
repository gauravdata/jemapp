<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from placement_overview_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'placement_overview_filter.tpl', 6, false),)), $this); ?>
<!--	placement_overview_filter		-->

<div>

<fieldset class="Filter">
    <legend><?php echo smarty_function_localize(array('str' => 'Reached/didn\'t reach conditions'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"reachedCondition\"></div>"; ?>
    </div>
</fieldset>

<fieldset class="Filter">
    <legend><?php echo smarty_function_localize(array('str' => 'Order ID'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"orderid\"></div>"; ?>
    </div>
    <?php echo smarty_function_localize(array('str' => 'You can input multiple order IDs separated either by new line or comma'), $this);?>

</fieldset>

<div style="clear: both;"></div>

</div>