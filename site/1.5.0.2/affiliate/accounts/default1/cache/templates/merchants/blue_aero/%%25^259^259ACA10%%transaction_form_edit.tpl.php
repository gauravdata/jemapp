<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from transaction_form_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'transaction_form_edit.tpl', 5, false),)), $this); ?>
<!-- transaction_form_edit -->

<div class="MandatoryFields">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Mandatory fields'), $this);?>
</legend>
<?php echo "<div id=\"userid\"></div>"; ?>
<?php echo "<div id=\"campaignid\"></div>"; ?>
<?php echo "<div id=\"dateinserted\"></div>"; ?>
<?php echo "<div id=\"rtype\"></div>"; ?>
<?php echo "<div id=\"rstatus\" class=\"StatusListBox\"></div>"; ?>
<?php echo "<div id=\"commtypeid\"></div>"; ?>
<?php echo "<div id=\"bannerid\"></div>"; ?>
<?php echo "<div id=\"channel\"></div>"; ?>
<?php echo "<div id=\"totalcost\" class=\"MFields\"></div>"; ?>
<?php echo "<div id=\"fixedcost\" class=\"MFields\"></div>"; ?>
<?php echo "<div id=\"commission\" class=\"MFields2\"></div>"; ?>
<?php echo "<div id=\"commissionTag\"></div>"; ?> 
<?php echo "<div id=\"multiTier\"></div>"; ?>
<?php echo "<div id=\"ComputeCommissionsButton\"></div>"; ?>
</fieldset>
</div>


<table border=0 cellpadding=0 cellspacing=0>
<tr>
<td>
<div class="OptionalFields">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Optional fields'), $this);?>
</legend>
<?php echo "<div id=\"orderid\" class=\"OFields\"></div>"; ?>
<?php echo "<div id=\"productid\" class=\"OFields\"></div>"; ?>
<?php echo "<div id=\"payoutstatus\"></div>"; ?>
<?php echo "<div id=\"countrycode\"></div>"; ?>
</fieldset>
</div>

</td><td>

<div class="OptionalFieldsRight">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Optional fields'), $this);?>
</legend>
<?php echo "<div id=\"data1\"></div>"; ?>
<?php echo "<div id=\"data2\"></div>"; ?>
<?php echo "<div id=\"data3\"></div>"; ?>
<?php echo "<div id=\"data4\"></div>"; ?>
<?php echo "<div id=\"data5\"></div>"; ?>
</fieldset>
</div>

</td>
</tr>
</table>