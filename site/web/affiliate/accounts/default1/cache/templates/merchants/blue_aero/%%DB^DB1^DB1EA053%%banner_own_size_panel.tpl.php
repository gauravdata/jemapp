<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from banner_own_size_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_own_size_panel.tpl', 2, false),)), $this); ?>
<!-- banner_own_size_panel -->
<div class="Inliner"><?php echo smarty_function_localize(array('str' => 'width'), $this);?>
</div>
<?php echo "<div id=\"size_width\" class=\"FormFieldSmallInline FormFieldOnlyInput\"></div>"; ?>
<div class="Inliner"><?php echo smarty_function_localize(array('str' => 'height'), $this);?>
</div>
<?php echo "<div id=\"size_height\" class=\"FormFieldSmallInline FormFieldOnlyInput\"></div>"; ?>