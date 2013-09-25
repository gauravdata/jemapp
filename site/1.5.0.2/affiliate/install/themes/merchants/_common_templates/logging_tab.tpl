<!-- logging_tab -->
<div class="TabDescription">

<div class="Log">
<fieldset>
<legend>##Log level##</legend>
{widget id="log_level"}
</fieldset>
</div>

<div class="Debug">
<fieldset>
<legend>##Debug##</legend>
##Debugging can be used for troubleshooting. If you turn it on, the system will log every action to your History log. You can use these messages to investigate the flow of commands, and to find out what is wrong. This way you can check what are the scripts doing and where exactly they fail. In production it should be turned off, because it generates multiple history records for each transaction and slows down the system.##
{widget id="aditionalDescription"}
{widget id="panelDebugSpecialSettings"}
</fieldset>
</div>

<fieldset>
    <legend>##Delete old events##</legend>
    <div class="Inliner"><div class="Label">##Delete event records older than##</div></div>
    <div class="FormFieldSmallInline">{widget id="deleteeventdays"}</div><div class="Inliner">##days##</div>
    <div class="Inliner">{widget id="helpAutoDeleteEvents"}</div>
    <div class="clear"></div>
    <div class="Inliner"><div class="Label">##Truncate all events if there are more than ##</div></div>
    <div class="FormFieldSmallInline">{widget id="deleteeventrecords"}</div><div class="Inliner">##records in logs table##</div>
    <div class="Inliner">{widget id="helpAutoDeleteEventsMaxRecordsCount"}</div>
    <div class="clear"></div>
</fieldset>

<fieldset>
    <legend>##Delete logins history##</legend>
    <div class="Inliner"><div class="Label">##Delete logins history older than##</div></div>
    <div class="FormFieldSmallInline">{widget id="deleteloginshistorydays"}</div><div class="Inliner">##days##</div>
    <div class="Inliner">{widget id="helpAutoDeleteLoginsHistory"}</div>
    <div class="clear"></div>
</fieldset>

{widget id="SaveButton"}
</div>
<div class="clear"></div>
