<!-- htaccess_panel -->
<fieldset>
<legend>##SEO Links settings##</legend>
##Here you can specify how your links will look like.<br/>The link format will be: http://www.yoursite.com/prefixAFFILIATEIDseparatorBANNERIDsuffix<br/>for example: http://www.yoursite.com/ref/11111111/22222222.html##

{widget id="modrewrite_prefix"}
{widget id="modrewrite_separator"}
{widget id="modrewrite_suffix"}
{widget id="regenerateButton"}
</fieldset>

<fieldset>
<legend>##.htaccess code##</legend>
##For proper SEO links functionality, you have to make sure that your web server supports mod_rewrite and you have to create a .htaccess file to your web home directory, and copy & paste the code below to this file.<br/>If this file already exists, simply add the code below to the end.<br/>Make sure you backup this file before making any changes.##  
{widget id="htaccess_code" class="HtaccessTextArea"}
</fieldset>

<div class="clear"></div>

<div style="float:left">
    {widget id="SaveButton"}
</div>
<div style="float:left">
    {widget id="CancelButton"}
</div>
