<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:28
         compiled from commission_type_sail_detail.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'commission_type_sail_detail.tpl', 6, false),)), $this); ?>
<!-- commission_type_sail_detail -->
<table width="100%" class="CommissionTypeDetailLine">
<tr>
<td width="80px"><?php echo "<div id=\"Logo\"></div>"; ?></td>
<td width="150px">
	<div class="CommissionTypeInfo"><div class="CommissionTypeText"><?php echo smarty_function_localize(array('str' => 'Type:'), $this);?>
</div><?php echo "<div id=\"TypeName\"></div>"; ?></div>
	<div><?php echo smarty_function_localize(array('str' => 'ID:'), $this);?>
 <?php echo "<div id=\"TypeId\"></div>"; ?></div>
</td>
<td width="350px">

<table width="100%" class="CommissionTypeTiers">
<tr><td colspan="2" class="CommissionFirst"><?php echo smarty_function_localize(array('str' => 'Commission:'), $this);?>
 <b><?php echo "<div id=\"CommissionValue\"></div>"; ?></b>, <?php echo smarty_function_localize(array('str' => 'Fixed cost:'), $this);?>
 <b><?php echo "<div id=\"FixedcostValue\"></div>"; ?></b></td></tr>
<tr class="CommissionOther">
  <td><div class="CommissionOtherItem"><?php echo smarty_function_localize(array('str' => '2nd tier commission:'), $this);?>
 <?php echo "<div id=\"2TierCommissionsValue\"></div>"; ?></div></td>
  <td><div class="CommissionOtherItem"><?php echo smarty_function_localize(array('str' => '3rd tier commission:'), $this);?>
 <?php echo "<div id=\"3TierCommissionsValue\"></div>"; ?></div></td>
</tr>
<tr class="CommissionOther">
  <td><div class="CommissionOtherItem"><?php echo smarty_function_localize(array('str' => '4th tier commission:'), $this);?>
 <?php echo "<div id=\"4TierCommissionsValue\"></div>"; ?></div></td>
  <td><div class="CommissionOtherItem"><?php echo smarty_function_localize(array('str' => '5th tier commission:'), $this);?>
 <?php echo "<div id=\"5TierCommissionsValue\"></div>"; ?> <?php echo "<div id=\"NextTiers\"></div>"; ?></div></td>
</tr>
</table>

</td>
<td class="CommissionActions"><?php echo "<div id=\"ButtonEdit\"></div>"; ?> <?php echo "<div id=\"ButtonEnable\"></div>"; ?></td>
</tr>
</table>