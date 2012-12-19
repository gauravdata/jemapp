<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:53
         compiled from welcome_message_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'welcome_message_panel.tpl', 4, false),)), $this); ?>
<!--    welcome_message_panel   -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Welcome message'), $this);?>
</legend>
    <?php echo "<div id=\"WelcomeMessage\" class=\"WelcomeMessage\"></div>"; ?>
</fieldset>