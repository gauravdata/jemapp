<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:19
         compiled from header.stpl */ ?>
<!-- header -->
<div class="Header">

<div class="HeaderInfo">
	<strong><a class="Logo" title="<?php echo $this->_tpl_vars['programName']; ?>
" href="index.php"/>
	           <img src="<?php echo $this->_tpl_vars['programLogo']; ?>
" class="LogoImage"></a></strong>
</div>
  <?php if ($this->_tpl_vars['isLogged'] == 1): ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'topmenu_logged.stpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php else: ?>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'topmenu_notlogged.stpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endif; ?>
</div>