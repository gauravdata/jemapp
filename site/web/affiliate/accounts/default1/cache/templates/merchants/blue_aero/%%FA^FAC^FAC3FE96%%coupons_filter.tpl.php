<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:38
         compiled from coupons_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'coupons_filter.tpl', 5, false),)), $this); ?>
<!--    coupons_filter  -->

<div>
    <fieldset class="Filter FilterStatus">
        <legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
        <div class="Resize">
            <?php echo "<div id=\"status\"></div>"; ?>
        </div>
    </fieldset>

    <fieldset class="Filter FilterCustom">
        <legend><?php echo smarty_function_localize(array('str' => 'Custom'), $this);?>
</legend>
        <div class="Resize">
            <?php echo "<div id=\"custom\"></div>"; ?>
        </div>
    </fieldset>
</div>
<div style="clear: both;"></div>