<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:51
         compiled from add_gadget_select_url.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'add_gadget_select_url.tpl', 2, false),)), $this); ?>
<!-- add_gadget_select_url -->
<p><?php echo smarty_function_localize(array('str' => 'Here you can add any UWA compatible gadget from google or netvibes or RSS feed not listed in our library. To add gadget, define name and input URL to gadget definition file.'), $this);?>
</p>
<?php echo "<div id=\"name\"></div>"; ?>
<?php echo "<div id=\"url\"></div>"; ?>
<?php echo "<div id=\"NextButton\"></div>"; ?>