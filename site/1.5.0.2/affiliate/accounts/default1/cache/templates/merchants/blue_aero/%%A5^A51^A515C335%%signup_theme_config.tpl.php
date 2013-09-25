<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from signup_theme_config.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'signup_theme_config.tpl', 4, false),)), $this); ?>
<!-- signup_theme_config -->

<div class="TabDescription">
<?php echo smarty_function_localize(array('str' => '<h3>Signup Form design</h3>
The design can be divided into several parts. The form itself is displayed using template. Look of the whole page is controlled by theme and it\'s templates.'), $this);?>

</div>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Signup form template'), $this);?>
</legend>
<div class="TabDescription">
<?php echo smarty_function_localize(array('str' => 'The template of your Signup Form defines the form, sections and fields.'), $this);?>
 
</div>
<?php echo "<div id=\"EditTemplateButton\"></div>"; ?>
</fieldset>


<br/><br/>
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Other available themes'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'You can choose from the themes below. Click on <strong>Select this theme</strong> to set it as a new default theme.'), $this);?>

<?php echo "<div id=\"otherThemes\"></div>"; ?>
</fieldset>