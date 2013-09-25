<?php /* Smarty version 2.6.18, created on 2011-08-17 23:00:51
         compiled from static_page_includes.stpl */ ?>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <meta http-Equiv="Cache-Control" Content="no-cache"/>
        <meta http-Equiv="Pragma" Content="no-cache"/>
        <meta http-Equiv="Expires" Content="0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <title><?php echo $this->_tpl_vars['title']; ?>
</title>
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