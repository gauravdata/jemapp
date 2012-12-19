<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from daily_report_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'daily_report_filter.tpl', 4, false),)), $this); ?>
<!-- daily_report_filter -->
<div>
    <fieldset class="Filter">
        <legend><?php echo smarty_function_localize(array('str' => 'Channel'), $this);?>
</legend>
        <div class="Resize"><?php echo "<div id=\"channel\"></div>"; ?></div>
    </fieldset>
    <fieldset class="Filter">
        <legend><?php echo smarty_function_localize(array('str' => 'Date'), $this);?>
</legend>
        <div class="Resize"><?php echo "<div id=\"date\"></div>"; ?></div>
    </fieldset>
</div>
<div style="clear: both;"></div>