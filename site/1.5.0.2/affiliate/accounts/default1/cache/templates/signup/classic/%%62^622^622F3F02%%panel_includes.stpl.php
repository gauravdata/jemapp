<?php /* Smarty version 2.6.18, created on 2011-08-18 03:20:22
         compiled from panel_includes.stpl */ ?>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <meta http-Equiv="Cache-Control" Content="no-cache"/>
        <meta http-Equiv="Pragma" Content="no-cache"/>
        <meta http-Equiv="Expires" Content="0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="robots" content="none"/>
        <meta name="gwt:property" content="panel=<?php echo $this->_tpl_vars['panel']; ?>
"/>
        <title><?php echo $this->_tpl_vars['title']; ?>
</title>
        <link type="image/png" href="<?php echo $this->_tpl_vars['faviconUrl']; ?>
" rel="shortcut icon"/>
        <link type="image/png" href="<?php echo $this->_tpl_vars['faviconUrl']; ?>
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