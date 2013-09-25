<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from recurring_commission_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'recurring_commission_filter.tpl', 4, false),)), $this); ?>
<!-- recurring_commission_filter -->

<fieldset class="Filter FilterDate">
    <legend><?php echo smarty_function_localize(array('str' => 'Date created'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"datecreated\"></div>"; ?>
    </div>
</fieldset>
   
<fieldset class="Filter FilterDate">
    <legend><?php echo smarty_function_localize(array('str' => 'Last commission date'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"datelastcommission\"></div>"; ?>
    </div>
</fieldset>

<fieldset class="Filter FilterStatus">
    <legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"rstatus\"></div>"; ?>
    </div>
</fieldset>

<fieldset class="Filter FilterRecurrence">
    <legend><?php echo smarty_function_localize(array('str' => 'Recurrence'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"recurrencepresetid\"></div>"; ?>
    </div>
</fieldset>
                
<fieldset class="Filter FilterCustom">
    <legend><?php echo smarty_function_localize(array('str' => 'Custom'), $this);?>
</legend>
    <div class="Resize">
        <?php echo "<div id=\"custom\"></div>"; ?>
    </div>
</fieldset>

<div style="clear: both;"></div>