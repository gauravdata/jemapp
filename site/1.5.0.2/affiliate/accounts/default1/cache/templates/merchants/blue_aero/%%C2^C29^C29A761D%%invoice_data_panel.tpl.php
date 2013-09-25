<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from invoice_data_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'invoice_data_panel.tpl', 3, false),)), $this); ?>
<!--	invoice_data_panel	-->

<div class="left"><?php echo smarty_function_localize(array('str' => 'Invoice number'), $this);?>
<?php echo "<div id=\"number\"></div>"; ?></div> 
<div class="right"><?php echo smarty_function_localize(array('str' => 'Amount'), $this);?>
<?php echo "<div id=\"amount\"></div>"; ?></div>
<div style="clear: both"></div>
<hr/>
<br/>
<div class="left"><?php echo smarty_function_localize(array('str' => 'Date from'), $this);?>
<?php echo "<div id=\"datefrom\"></div>"; ?></div>
<div class="right"><?php echo smarty_function_localize(array('str' => 'Due date'), $this);?>
<?php echo "<div id=\"duedate\"></div>"; ?></div>
<div class="left"><?php echo smarty_function_localize(array('str' => 'Date to'), $this);?>
<?php echo "<div id=\"dateto\"></div>"; ?></div>
<div class="right"><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
<?php echo "<div id=\"rstatus\"></div>"; ?></div>
<div style="clear: both"></div>
<br/>
<?php echo smarty_function_localize(array('str' => 'Invoice note'), $this);?>
<?php echo "<div id=\"merchantnote\"></div>"; ?><br/>
<?php echo "<div id=\"proformatext\" class=\"Invoice\"></div>"; ?><?php echo "<div id=\"invoicetext\" class=\"Invoice\"></div>"; ?>
<br/>
<?php echo "<div id=\"print\"></div>"; ?><?php echo "<div id=\"close\"></div>"; ?><?php echo "<div id=\"send_mail\"></div>"; ?>