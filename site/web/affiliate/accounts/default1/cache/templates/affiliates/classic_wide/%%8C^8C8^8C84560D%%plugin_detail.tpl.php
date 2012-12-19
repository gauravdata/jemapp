<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:35
         compiled from plugin_detail.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'plugin_detail.tpl', 8, false),)), $this); ?>
<!-- plugin_detail -->
<table class="PluginDetail">
	<tr>
		<td width="60px"><?php echo "<div id=\"Logo\"></div>"; ?></td>
		<td width="450px" class="PluginData">
			<div class="PluginHeader">
				<div class="PluginName"><?php echo "<div id=\"Name\"></div>"; ?></div>
				<div class="PluginInfo"><?php echo smarty_function_localize(array('str' => 'version'), $this);?>
 <?php echo "<div id=\"Version\"></div>"; ?> / <?php echo "<div id=\"Author\"></div>"; ?></div>
				<div class="clear"></div>
			</div>
		    <div class="PluginDescription"><?php echo "<div id=\"Description\"></div>"; ?></div>
		</td>
		<td width="100px" class="PluginMoreInfo"><?php echo "<div id=\"Help\"></div>"; ?><?php echo "<div id=\"Settings\"></div>"; ?></td>
		<td width="150px" class="PluginActions"><?php echo "<div id=\"Actions\"></div>"; ?></td>
	</tr>
</table>



<!--
<div class="PluginDetail">
</div>
-->