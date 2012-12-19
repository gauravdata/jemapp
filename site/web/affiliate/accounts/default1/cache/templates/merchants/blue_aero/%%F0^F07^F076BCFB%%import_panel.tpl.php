<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from import_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'import_panel.tpl', 3, false),)), $this); ?>
<!--    import_panel    -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Import source'), $this);?>
</legend>
    <?php echo "<div id=\"delimiter\"></div>"; ?>
    <?php echo "<div id=\"source\" class=\"ImportRadioGroup\"></div>"; ?>
    <?php echo "<div id=\"url\"></div>"; ?>
    <?php echo "<div id=\"uploadFile\"></div>"; ?>
    <?php echo "<div id=\"exportFilesGrid\"></div>"; ?> 
    <?php echo "<div id=\"serverFile\"></div>"; ?>
</fieldset>
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Drop modules'), $this);?>
</legend>
    <?php echo "<div id=\"importExportGrid\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"fileName\"></div>"; ?>
<?php echo "<div id=\"dropModules\"></div>"; ?>
    
<?php echo "<div id=\"importButton\"></div>"; ?>