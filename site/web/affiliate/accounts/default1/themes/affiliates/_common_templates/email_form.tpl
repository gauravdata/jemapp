<!-- email_form -->
<div class="EmailForm">

<fieldset>
<legend>##Mail##</legend>
    {widget id="subject"}

	{widget id="body_html"}
	{widget id="body_text"}
	{widget id="customTextBodyControl" class="EmailForm" class="EmailFormControlTextBody"}
</fieldset>

<fieldset>
<legend>##Attachments##</legend>
    {widget id="uploadedFiles"}
</fieldset>
</div>
{widget id="clearButton"}
{widget id="loadTemplateButton"}
