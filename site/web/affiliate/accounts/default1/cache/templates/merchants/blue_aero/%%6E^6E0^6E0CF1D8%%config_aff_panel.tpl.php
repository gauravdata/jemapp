<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:28
         compiled from config_aff_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'config_aff_panel.tpl', 9, false),)), $this); ?>
<!-- config_aff_panel -->
<div class="overviewDataBox" style="width:1000px;">
    <div class="overviewInnerBox">
    <div class="BoxTopLeft"><div class="BoxTopRight"><div class="BoxTop"></div></div></div>
	<div class="BoxLeft"><div class="BoxRight">
	<div class="BoxMain" style="min-height: 80px; padding: 5px 0 5px 28px;">
	
<table>
	<tr><td><?php echo smarty_function_localize(array('str' => 'URL to Affiliate Panel:'), $this);?>
</td><td><?php echo "<div id=\"AffiliatePanelUrl\"></div>"; ?></td></tr>

<tr><td><?php echo smarty_function_localize(array('str' => 'URL to Affiliate mini site:'), $this);?>
</td><td><?php echo "<div id=\"AffiliateSiteUrl\"></div>"; ?></td></tr>
</table>
<?php echo smarty_function_localize(array('str' => 'Note that informational mini site is optional, the files don\'t need to be there'), $this);?>



	</div></div></div>
	<div class="BoxBottomLeft"><div class="BoxBottomRight"><div class="BoxBottom"></div></div></div></div>
</div>

<div class="clear"></div>

<?php echo "<div id=\"Tabs\"></div>"; ?>