<!-- commission_type_sail_detail -->
<table width="100%" class="CommissionTypeDetailLine">
<tr>
<td width="80px">{widget id="Logo"}</td>
<td width="150px">
	<div class="CommissionTypeInfo"><div class="CommissionTypeText">##Type:##</div>{widget id="TypeName"}</div>
	<div>##ID:## {widget id="TypeId"}</div>
</td>
<td width="350px">

<table width="100%" class="CommissionTypeTiers">
<tr><td colspan="2" class="CommissionFirst">##Commission:## <b>{widget id="CommissionValue"}</b>, ##Fixed cost:## <b>{widget id="FixedcostValue"}</b></td></tr>
<tr class="CommissionOther">
  <td><div class="CommissionOtherItem">##2nd tier commission:## {widget id="2TierCommissionsValue"}</div></td>
  <td><div class="CommissionOtherItem">##3rd tier commission:## {widget id="3TierCommissionsValue"}</div></td>
</tr>
<tr class="CommissionOther">
  <td><div class="CommissionOtherItem">##4th tier commission:## {widget id="4TierCommissionsValue"}</div></td>
  <td><div class="CommissionOtherItem">##5th tier commission:## {widget id="5TierCommissionsValue"} {widget id="NextTiers"}</div></td>
</tr>
</table>

</td>
<td class="CommissionActions">{widget id="ButtonEdit"} {widget id="ButtonEnable"}</td>
</tr>
</table>
