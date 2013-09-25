<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:28
         compiled from commission_type_detail.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'commission_type_detail.tpl', 6, false),)), $this); ?>
<!-- commission_type_detail -->
<table width="100%" class="CommissionTypeDetailLine">
<tr>
<td width="80px"><?php echo "<div id=\"Logo\"></div>"; ?></td>
<td width="150px">
    <div class="CommissionTypeInfo"><div class="CommissionTypeText"><?php echo smarty_function_localize(array('str' => 'Type:'), $this);?>
</div><?php echo "<div id=\"TypeName\"></div>"; ?><div class="clear"></div></div>
    <div class="CommissionIDInfo"><div class="CommissionIDText"><?php echo smarty_function_localize(array('str' => 'ID:'), $this);?>
</div><?php echo "<div id=\"TypeId\"></div>"; ?><div class="clear"></div></div>
</td>
<td width="350px">

<?php echo "<div id=\"commissionTiersInfoPanel\"></div>"; ?>

</td>
<td class="CommissionActions"><?php echo "<div id=\"buttonsPanel\"></div>"; ?></td>
</tr>
<tr>
<td colspan="4"><?php echo "<div id=\"ExtensionPanel\"></div>"; ?></td>
</tr>
</table>