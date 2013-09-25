<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:42
         compiled from stats_chart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'stats_chart.tpl', 11, false),)), $this); ?>
<!-- stats_chart -->

<div class="StatsChartHolder">
<table class="StatsChart">
<tr>
  <td align="left" width="30%">
    <div style="width:200px;"><?php echo "<div id=\"LabelFromTo\"></div>"; ?></div></td>
  <td width="70%" align="right">
      <table>
        <tr>
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
      </table>
  </td>
</tr>
<tr>
  <td colspan="2" align="left"><?php echo "<div id=\"Chart\"></div>"; ?></td>
</tr>
</table>
</div>