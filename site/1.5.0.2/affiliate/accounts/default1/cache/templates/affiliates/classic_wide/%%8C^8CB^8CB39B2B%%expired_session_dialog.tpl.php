<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:40
         compiled from expired_session_dialog.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'expired_session_dialog.tpl', 3, false),)), $this); ?>
<!-- expired_session_dialog -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Response returned from server'), $this);?>
</legend>
    <?php echo "<div id=\"message\"></div>"; ?>
</fieldset>
<?php echo smarty_function_localize(array('str' => 'Do you like to reload application now ?'), $this);?>

<?php echo "<div id=\"ok\"></div>"; ?>