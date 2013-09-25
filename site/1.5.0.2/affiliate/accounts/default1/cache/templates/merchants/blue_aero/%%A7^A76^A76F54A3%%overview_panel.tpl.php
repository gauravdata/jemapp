<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from overview_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'overview_panel.tpl', 5, false),)), $this); ?>
<!-- overview_panel -->

<?php echo "<div id=\"CustomPanel\"></div>"; ?>

<strong class="QuickLinks"><?php echo smarty_function_localize(array('str' => 'Quick links'), $this);?>
</strong>
<div class="LineShort"></div>
<?php echo "<div id=\"IconPanel\"></div>"; ?>
