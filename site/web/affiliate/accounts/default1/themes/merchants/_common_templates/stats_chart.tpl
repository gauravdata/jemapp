<!-- stats_chart -->

<div class="StatsChartHolder">
<table class="StatsChart">
<tr>
  <td align="left" width="30%">
    <div style="width:200px;">{widget id="LabelFromTo"}</div></td>
  <td width="70%" align="right">
      <table>
        <tr>
            <td align="right" width="60" nowrap>##Chart:##</td>
            <td align="left" width="150">{widget id="ChartType"}</td>
            <td align="right" width="70" nowrap>##Group by:##</td>
            <td align="left" width="160">{widget id="GroupBy"}</td>
            <td align="right" width="60" nowrap>##Data:##</td>
            <td align="left" width="140" class="StatsDataType">{widget id="DataType"}</td>
        </tr>
      </table>
  </td>
</tr>
<tr>
  <td colspan="2" align="left">{widget id="Chart"}</td>
</tr>
</table>
</div>
