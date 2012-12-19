<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:28
         compiled from commission_group_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'commission_group_panel.tpl', 7, false),)), $this); ?>
<!--    commission_group_panel  -->

<?php echo "<div id=\"campaignDetails\"></div>"; ?>
<?php echo "<div id=\"backButton\"></div>"; ?>

<fieldset>       
    <legend><?php echo smarty_function_localize(array('str' => 'Commission group'), $this);?>
</legend>
    <?php echo "<div id=\"name\"></div>"; ?>
    <?php echo "<div id=\"priority\"></div>"; ?>
    <?php echo "<div id=\"cookielifetime\"></div>"; ?>
    <br/>
    <?php echo "<div id=\"tabPanel\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"saveButton\"></div>"; ?>