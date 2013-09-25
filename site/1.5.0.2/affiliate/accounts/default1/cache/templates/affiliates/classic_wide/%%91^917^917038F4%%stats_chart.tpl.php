<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:53
         compiled from stats_chart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'stats_chart.tpl', 8, false),)), $this); ?>
<!-- stats_chart -->

<div class="StatsChartHolder">
<table class="StatsChart">
<tr>
  <td align="left" width="200"><?php echo "<div id=\"LabelFromTo\"></div>"; ?></td>
  <td>&nbsp;</td>
  <td align="right" width="60" nowrap><?php echo smarty_function_localize(array('str' => 'Chart:'), $this);?>
</td>
  <td align="left" width="150"><?php echo "<div id=\"ChartType\"></div>"; ?></td>
  <td align="right" width="70" nowrap><?php echo smarty_function_localize(array('str' => 'Group by:'), $this);?>
</td>
  <td align="left" width="160"><?php echo "<div id=\"GroupBy\"></div>"; ?></td>
  <td align="right" width="60" nowrap><?php echo smarty_function_localize(array('str' => 'Data:'), $this);?>
</td>
  <td align="left" width="140" class="StatsDataType"><?php echo "<div id=\"DataType\"></div>"; ?></td>
</tr>
<tr>
  <td colspan="12" align="left"><?php echo "<div id=\"Chart\"></div>"; ?></td>
</tr>
</table>
</div>