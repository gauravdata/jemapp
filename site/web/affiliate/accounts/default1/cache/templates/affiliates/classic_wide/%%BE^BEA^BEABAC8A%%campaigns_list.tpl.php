<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:53
         compiled from campaigns_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'campaigns_list.tpl', 2, false),)), $this); ?>
<!-- campaigns_list -->
<div class="QuickNavigationIcons"><?php echo smarty_function_localize(array('str' => 'Quick Navigation Icons'), $this);?>
</div>
<div class="IconsPanel">
	<?php echo "<div id=\"Banners\"></div>"; ?>
	<?php echo "<div id=\"DirectLinks\"></div>"; ?>
	<?php echo "<div id=\"Channels\"></div>"; ?>
	<?php echo "<div id=\"AffLinkProtector\"></div>"; ?>
	<?php echo "<div id=\"Reports\"></div>"; ?>
</div>	
<div class="clear"></div>
<?php echo "<div id=\"CampaignsFilter\"></div>"; ?>
<div class="clear"></div>
<?php echo "<div id=\"CampaignsGrid\"></div>"; ?>