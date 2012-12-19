<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:28
         compiled from commission_tiers_info_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'commission_tiers_info_panel.tpl', 3, false),)), $this); ?>
<!-- commission_tiers_info_panel -->
<table width="100%" class="CommissionTypeTiers">
<tr><td colspan="2" class="CommissionFirst"><?php echo smarty_function_localize(array('str' => 'Commission:'), $this);?>
 <b><?php echo "<div id=\"CommissionValue\"></div>"; ?></b></td></tr>
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