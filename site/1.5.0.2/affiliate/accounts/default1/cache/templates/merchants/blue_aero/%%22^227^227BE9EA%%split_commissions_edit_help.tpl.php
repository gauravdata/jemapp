<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from split_commissions_edit_help.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'split_commissions_edit_help.tpl', 3, false),)), $this); ?>
<!-- split_commissions_edit -->
<fieldset>
    <b><?php echo smarty_function_localize(array('str' => 'Split Commissions Graph'), $this);?>
</b>
    <br><br>
    <?php echo smarty_function_localize(array('str' => 'First row of graph represents how sale commission is divided between first affiliate, 
    last affiliate and other affiliates.'), $this);?>

    <br>
    <?php echo smarty_function_localize(array('str' => 'Second row represents which part of commissions is divided between all 
    affiliates and which part is directyly added to first and last affiliates.'), $this);?>

    <br>
    <?php echo smarty_function_localize(array('str' => 'You can see that commission for first(last) affiliate is not just first(last) click 
    bonus but also part of "commissions for others" is added to it.'), $this);?>

    <br><br>
    <?php echo "<div id=\"SplitCommissionsBar\"></div>"; ?>
    <br>
    <b><?php echo smarty_function_localize(array('str' => 'Example'), $this);?>
</b> <br>
    <?php echo "<div id=\"SplitCommissionsExample\"></div>"; ?>
    <br>
    <?php echo "<div id=\"SplitCommissionsLink\"></div>"; ?>
    
</fieldset>   