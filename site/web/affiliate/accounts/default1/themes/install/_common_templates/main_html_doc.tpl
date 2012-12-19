<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <meta http-Equiv="Cache-Control" Content="no-cache"/>
        <meta http-Equiv="Pragma" Content="no-cache"/>
        <meta http-Equiv="Expires" Content="0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="robots" content="none"/>
		<meta name="description" content="{$metaDescription}" />
		<meta name="keywords" content="{$metaKeywords}" />
        
        <title>{$title}</title>
        <link href="{$faviconUrl}" rel="shortcut icon"/>
        <link href="{$faviconUrl}" rel="icon"/>
        {$cachedData}
        {if $gwtModuleName}        
        <script type="text/javascript" src="{$gwtModuleName}"></script>        
        {/if}
        
        <style type="text/css" media="all">
            {foreach from=$stylesheets item=stylesheet}
            @import "{$stylesheet}";
            {/foreach}
        </style>
        {foreach from=$jsResources item=jsResource}
            <script src="{$jsResource.resource}" type="text/javascript" {if $jsResource.id ne null}id="{$jsResource.id}"{/if}></script>       
        {/foreach}
        {foreach from=$jsScripts item=jsScript}
        <script type="text/javascript">
            {$jsScript}
        </script>       
        {/foreach}    </head>
    <body>
        <iframe src="javascript:''" id="__gwt_historyFrame" style="width:0;height:0;border:0"></iframe>
        {$body}
    </body>
</html>
