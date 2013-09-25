<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:21
         compiled from language_translations_grid.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'language_translations_grid.tpl', 2, false),)), $this); ?>
<!-- language_translations_grid -->
<?php echo smarty_function_localize(array('str' => 'To change translation just click on translation column. After you will change translation text, click button Save modified rows below table'), $this);?>
<br/>
<?php echo "<div id=\"SearchAndFilter\"></div>"; ?>
<?php echo "<div id=\"translationsGrid\" class=\"TranslationsGrid\"></div>"; ?>