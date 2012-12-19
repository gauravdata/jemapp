<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:50
         compiled from banners.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banners.tpl', 2, false),)), $this); ?>
<!-- banners -->
<div class="QuickNavigationIcons"><?php echo smarty_function_localize(array('str' => 'Quick Navigation Icons'), $this);?>
</div>
<div class="IconsPanel">	
	<?php echo "<div id=\"Campaigns\"></div>"; ?>
	<?php echo "<div id=\"DirectLinks\"></div>"; ?>
	<?php echo "<div id=\"Channels\"></div>"; ?>
	<?php echo "<div id=\"AffLinkProtector\"></div>"; ?>
	<?php echo "<div id=\"Reports\"></div>"; ?>
</div>
<div class="clear"></div>

<div class="BannerFilterAndGrid">
<?php echo "<div id=\"BannersFilter\"></div>"; ?>
<?php echo "<div id=\"BannersGrid\"></div>"; ?>
</div>