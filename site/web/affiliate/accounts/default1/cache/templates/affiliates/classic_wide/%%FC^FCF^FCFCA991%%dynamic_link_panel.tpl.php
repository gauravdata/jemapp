<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:03
         compiled from dynamic_link_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'dynamic_link_panel.tpl', 4, false),)), $this); ?>
<!--    dynamic_link_panel  -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Dynamic link'), $this);?>
</legend>
    <?php echo "<div id=\"desturl\"></div>"; ?>
    <?php echo "<div id=\"preview\" class=\"DynamicLinkPreview\"></div>"; ?>
    <?php echo "<div id=\"code\" class=\"DynamicLinkTextArea\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"message\"></div>"; ?>
<?php echo "<div id=\"getCodeButton\"></div>"; ?>
<?php echo "<div id=\"closeButton\"></div>"; ?>