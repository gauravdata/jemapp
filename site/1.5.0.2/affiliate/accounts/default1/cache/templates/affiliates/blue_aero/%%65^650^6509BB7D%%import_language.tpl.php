<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:21
         compiled from import_language.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'import_language.tpl', 5, false),)), $this); ?>
<!-- import_language -->

<div class="SystemLanguages">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Available languages'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'To make it easier for you, some languages are already included in the distribution'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'To import new language to the system, click on "Import" icon next to language that you would like to import.'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'Warning: If language with the same language code (e.g. [en-US]) already exists, translations and language metadata will be overwritten with the values loaded from imported language!!.'), $this);?>

<?php echo "<div id=\"SystemLanguagesGrid\"></div>"; ?>
</fieldset>
</div>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Upload custom language'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Import new language from the CSV file.'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'Warning: If language with the same language code (e.g. [en-US]) already exists, translations and language metadata will be overwritten with the values from the uploaded language!'), $this);?>

<?php echo "<div id=\"CustomLanguageUpload\"></div>"; ?>
<?php echo "<div id=\"uploadedLanguageFileForm\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"CloseButton\"></div>"; ?>