<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:42
         compiled from traffic_stats_in_gadget.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'traffic_stats_in_gadget.tpl', 4, false),)), $this); ?>
<!-- traffic_stats_in_gadget -->
<table>
<tr>
  <td><?php echo smarty_function_localize(array('str' => 'Impressions'), $this);?>
</td><td>&nbsp;&nbsp;</td><td nowrap><?php echo "<div id=\"countImpressions\"></div>"; ?></td>
</tr><tr>
  <td><?php echo smarty_function_localize(array('str' => 'Clicks'), $this);?>
</td><td>&nbsp;&nbsp;</td><td nowrap><?php echo "<div id=\"countClicks\"></div>"; ?></td>
</tr><tr>
  <td><?php echo smarty_function_localize(array('str' => ' # of Sales / Leads'), $this);?>
</td><td>&nbsp;&nbsp;</td><td nowrap><?php echo "<div id=\"countSales\"></div>"; ?></td>
</tr><tr>
  <td><?php echo smarty_function_localize(array('str' => 'Commissions'), $this);?>
</td><td>&nbsp;&nbsp;</td><td nowrap><?php echo "<div id=\"sumCommissions\"></div>"; ?></td>
</tr><tr>
  <td><?php echo smarty_function_localize(array('str' => 'Revenue'), $this);?>
</td><td>&nbsp;&nbsp;</td><td nowrap><?php echo "<div id=\"sumSales\"></div>"; ?></td>
</tr>
</table>