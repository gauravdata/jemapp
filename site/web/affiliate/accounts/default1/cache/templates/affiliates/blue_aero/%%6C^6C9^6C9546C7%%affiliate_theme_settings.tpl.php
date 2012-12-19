<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:41
         compiled from affiliate_theme_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_theme_settings.tpl', 3, false),)), $this); ?>
<!-- affiliate_theme_settings -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Selected theme'), $this);?>
</legend>
<?php echo "<div id=\"selectedTheme\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Other themes'), $this);?>
</legend>
<?php echo "<div id=\"otherThemes\"></div>"; ?>
</fieldset>