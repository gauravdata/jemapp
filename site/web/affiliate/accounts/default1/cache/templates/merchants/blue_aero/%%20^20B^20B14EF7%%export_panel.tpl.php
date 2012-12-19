<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from export_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'export_panel.tpl', 3, false),)), $this); ?>
<!--    export_panel    -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Build export'), $this);?>
</legend>
    <?php echo "<div id=\"delimiter\"></div>"; ?>
    <?php echo "<div id=\"note\"></div>"; ?>
    <?php echo "<div id=\"importExportGrid\"></div>"; ?>
    <?php echo "<div id=\"codes\"></div>"; ?>
    <div style="float: left">
        <?php echo "<div id=\"exportLabel\"></div>"; ?>
    </div>
    <div style="float: left">
        <?php echo "<div id=\"exportLink\"></div>"; ?>
    </div>
    <div style="clear: both;"></div>
    <?php echo "<div id=\"exportButton\"></div>"; ?>
</fieldset>