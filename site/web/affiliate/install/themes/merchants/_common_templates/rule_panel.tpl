<!--    rule_panel      -->

<div class="ScreenHeader RuleViewHeader">
    <div class="RuleRightIcons">  
        {widget id="RefreshButton"}  
    </div>
    <div class="ScreenTitle">
        {widget id="screenTitle"}
    </div>
    <div class="ScreenDescription">
       {widget id="screenDescription"}
    </div>
    <div class="clear"/>
</div>

{widget id="ruleConditions"}

<fieldset>
    <legend>##Actions##</legend>
    <table>
        <tr>
            <td>##then##</td>
            <td width="250">{widget id="action" class="ConditionListBox"}</td>
            <td>{widget id="commissiongroupid" class="ConditionListBox"}</td>
            <td>{widget id="bonustype" class="ActionBonusType"}</td>
            <td>{widget id="bonusvalue" class="ActionBonusValue"}</td>
        </tr>
    </table>
</fieldset>
{widget id="formmessage"}
{widget id="sendButton"}
{widget id="cancelButton"}
