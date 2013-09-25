<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:21
         compiled from languages_configuration.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'languages_configuration.tpl', 2, false),)), $this); ?>
<!-- languages_configuration -->
<h3 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Languages'), $this);?>
</h3>
<?php echo smarty_function_localize(array('str' => 'In the table below you can see all the languages loaded in the system.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'To import a new language, click on Import language button and follow the steps in the import wizard.'), $this);?>

<br/><br/>
<?php echo smarty_function_localize(array('str' => 'If you don\'t want to use some language, you can deactivate it by changing value Yes to No in the \'Is active\' column (don\'t forget to save your changes)'), $this);?>

<br/>

<h3 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Translations and default language'), $this);?>
</h3>
<?php echo smarty_function_localize(array('str' => 'One language must be system\'s default. To change the default language, click on icon \'Set as default\' next to the language you want to set as default.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'To change some translations, click on Edit icon and continue in the editation.'), $this);?>


<br/>
<h3 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Date & time format'), $this);?>
</h3>
<?php echo smarty_function_localize(array('str' => 'Every language has defined it\'s own date and time format. To modify it, click on the Edit icon next to the language you would like to change.'), $this);?>

<br/><br/>

<?php echo "<div id=\"LanguagesGrid\"></div>"; ?>
<div class="clearfix"></div>