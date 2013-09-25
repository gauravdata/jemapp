<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from language_edit_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'language_edit_panel.tpl', 2, false),)), $this); ?>
<!-- language_edit_panel -->
<h4 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Language details'), $this);?>
</h4>
    <?php echo "<div id=\"code\"></div>"; ?>
    <?php echo "<div id=\"name\"></div>"; ?>
    <?php echo "<div id=\"eng_name\"></div>"; ?>
    <?php echo "<div id=\"author\"></div>"; ?>
    <?php echo "<div id=\"version\"></div>"; ?>
    <?php echo "<div id=\"date_number_format_panel\"></div>"; ?>
    <?php echo "<div id=\"saveMetadata\"></div>"; ?>