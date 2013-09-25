<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:10
         compiled from newsletter_broadcast_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'newsletter_broadcast_form.tpl', 3, false),)), $this); ?>
<!-- newsletter_broadcast_form -->
<?php echo "<div id=\"emailForm\"></div>"; ?>
<?php echo smarty_function_localize(array('str' => 'Broadcast message will be delivered to all users signed into this newsletter.'), $this);?>

<div class="clear"></div>
<?php echo "<div id=\"SaveButton\"></div>"; ?> <?php echo "<div id=\"ClearButton\"></div>"; ?> <?php echo "<div id=\"LoadTemplateButton\"></div>"; ?>