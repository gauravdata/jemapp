<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from signup_form_design.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'signup_form_design.tpl', 3, false),)), $this); ?>
<!-- signup_form_design -->

<h3><?php echo smarty_function_localize(array('str' => 'Customize design of signup form'), $this);?>
</h3>
<?php echo smarty_function_localize(array('str' => 'Here you can change design of affiliate signup form by changing it\'s templates.'), $this);?>


<div class="designForm">

<div class="designHeader">
<div class="designLogo"><?php echo "<div id=\"ChangeLogo\"></div>"; ?></div>
<?php echo smarty_function_localize(array('str' => 'HEADER'), $this);?>
<br/><?php echo "<div id=\"EditHeader\"></div>"; ?>
</div>

<div class="designContent"><?php echo smarty_function_localize(array('str' => 'SIGNUP FORM FIELDS'), $this);?>
<br/><?php echo "<div id=\"EditSignupForm\"></div>"; ?></div>

<div class="designFooter"><?php echo smarty_function_localize(array('str' => 'FOOTER'), $this);?>
<br/><?php echo "<div id=\"EditFooter\"></div>"; ?></div>

</div>

