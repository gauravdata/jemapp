<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:21
         compiled from main_html_doc.tpl */ ?>
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
		<meta name="description" content="<?php echo $this->_tpl_vars['metaDescription']; ?>
" />
		<meta name="keywords" content="<?php echo $this->_tpl_vars['metaKeywords']; ?>
" />
        
        <title><?php echo $this->_tpl_vars['title']; ?>
</title>
        <link href="<?php echo $this->_tpl_vars['faviconUrl']; ?>
" rel="shortcut icon"/>
        <link href="<?php echo $this->_tpl_vars['faviconUrl']; ?>
" rel="icon"/>
        <?php echo $this->_tpl_vars['cachedData']; ?>

        <?php if ($this->_tpl_vars['gwtModuleName']): ?>        
        <script type="text/javascript" src="<?php echo $this->_tpl_vars['gwtModuleName']; ?>
"></script>        
        <?php endif; ?>
        
        <style type="text/css" media="all">
            <?php $_from = $this->_tpl_vars['stylesheets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['stylesheet']):
?>
            @import "<?php echo $this->_tpl_vars['stylesheet']; ?>
";
            <?php endforeach; endif; unset($_from); ?>
        </style>
        <?php $_from = $this->_tpl_vars['jsResources']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jsResource']):
?>
            <script src="<?php echo $this->_tpl_vars['jsResource']['resource']; ?>
" type="text/javascript" <?php if ($this->_tpl_vars['jsResource']['id'] != null): ?>id="<?php echo $this->_tpl_vars['jsResource']['id']; ?>
"<?php endif; ?>></script>       
        <?php endforeach; endif; unset($_from); ?>
        <?php $_from = $this->_tpl_vars['jsScripts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jsScript']):
?>
        <script type="text/javascript">
            <?php echo $this->_tpl_vars['jsScript']; ?>

        </script>       
        <?php endforeach; endif; unset($_from); ?>    </head>
    <body>
        <iframe src="javascript:''" id="__gwt_historyFrame" style="width:0;height:0;border:0"></iframe>
        <?php echo $this->_tpl_vars['body']; ?>

    </body>
</html>