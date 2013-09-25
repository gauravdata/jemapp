<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from htaccess_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'htaccess_panel.tpl', 3, false),)), $this); ?>
<!-- htaccess_panel -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'SEO Links settings'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Here you can specify how your links will look like.<br/>The link format will be: http://www.yoursite.com/prefixAFFILIATEIDseparatorBANNERIDsuffix<br/>for example: http://www.yoursite.com/ref/11111111/22222222.html'), $this);?>


<?php echo "<div id=\"modrewrite_prefix\"></div>"; ?>
<?php echo "<div id=\"modrewrite_separator\"></div>"; ?>
<?php echo "<div id=\"modrewrite_suffix\"></div>"; ?>
<?php echo "<div id=\"regenerateButton\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => '.htaccess code'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'For proper SEO links functionality, you have to make sure that your web server supports mod_rewrite and you have to create a .htaccess file to your web home directory, and copy & paste the code below to this file.<br/>If this file already exists, simply add the code below to the end.<br/>Make sure you backup this file before making any changes.'), $this);?>
  
<?php echo "<div id=\"htaccess_code\" class=\"HtaccessTextArea\"></div>"; ?>
</fieldset>

<div class="clear"></div>

<div style="float:left">
    <?php echo "<div id=\"SaveButton\"></div>"; ?>
</div>
<div style="float:left">
    <?php echo "<div id=\"CancelButton\"></div>"; ?>
</div>