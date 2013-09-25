<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:32
         compiled from send_to_friend.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'send_to_friend.tpl', 3, false),)), $this); ?>
<!-- send_to_friend -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Send to a friend'), $this);?>
</legend>
    <?php echo "<div id=\"recipients\" class=\"ContactUsText\"></div>"; ?>
    <?php echo "<div id=\"from\" class=\"ContactUsText\"></div>"; ?>
    <?php echo "<div id=\"subject\" class=\"ContactUsText\"></div>"; ?>
    <?php echo "<div id=\"message\" class=\"ContactUsText\"></div>"; ?>
    <?php echo "<div id=\"bannerId\"></div>"; ?>
    <?php echo "<div id=\"personalMessage\" class=\"ContactUsText\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>
<?php echo "<div id=\"CloseButton\"></div>"; ?>
<div class="clear"></div>