<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:35
         compiled from plugins_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'plugins_list.tpl', 3, false),)), $this); ?>
<!-- plugins_list -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Currently active plugins'), $this);?>
</legend>
<?php echo "<div id=\"PanelActive\" class=\"PanelActivePlugins\"></div>"; ?>
</fieldset>

<div class="clear"></div>
<br/><br/>
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Get More Plugins'), $this);?>
</legend>
<p>
<?php echo smarty_function_localize(array('str' => 'You can find additional plugins for your site in our plugin directory on page'), $this);?>
 <a href="<?php echo $this->_tpl_vars['qualityUnitAddonsLink']; ?>
" target="_blank"><?php echo $this->_tpl_vars['qualityUnitAddonsLink']; ?>
</a>
<br/>
<?php echo smarty_function_localize(array('str' => 'To install a plugin you generally just need to extract and upload the plugin file into your /plugins directory. Once a plugin is uploaded, you may activate it below.'), $this);?>

</p>
</fieldset>
<br/><br/>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Uploaded, but inactive plugins'), $this);?>
</legend>
<?php echo "<div id=\"PanelInactive\" class=\"PanelInactivePlugins\"></div>"; ?>
</fieldset>

<div class="clear"></div>