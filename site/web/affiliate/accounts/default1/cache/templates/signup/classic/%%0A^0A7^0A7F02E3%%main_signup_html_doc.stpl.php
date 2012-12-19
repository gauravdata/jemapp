<?php /* Smarty version 2.6.18, created on 2011-08-18 03:20:22
         compiled from main_signup_html_doc.stpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'panel_includes.stpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </head>
    <body>
        <div>
        	<input type="hidden" id="visitorId" value="" />
        	<script id="pap_x2s6df8d" src="<?php echo $this->_tpl_vars['basePath']; ?>
scripts/salejs.php" type="text/javascript">
        	</script>
        	<script type="text/javascript">
            	PostAffTracker.writeCookieToCustomField("visitorId");
    	    </script>
    	</div>
    
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.stpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <iframe src="javascript:''" id="__gwt_historyFrame" style="width:0;height:0;border:0"></iframe>
        <?php echo $this->_tpl_vars['body']; ?>

        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'panel_footer.stpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </body>
</html>