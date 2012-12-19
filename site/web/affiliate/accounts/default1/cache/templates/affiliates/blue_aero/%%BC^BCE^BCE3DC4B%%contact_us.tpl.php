<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from contact_us.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'contact_us.tpl', 3, false),)), $this); ?>
<!-- contact_us -->
<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'Contact us'), $this);?>
</legend>
	<?php echo smarty_function_localize(array('str' => 'You can contact us directly using this contact form'), $this);?>

	<?php echo "<div id=\"subject\" class=\"ContactUsText\"></div>"; ?>
	<?php echo "<div id=\"text\" class=\"ContactUsText\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>
<div class="clear"></div>