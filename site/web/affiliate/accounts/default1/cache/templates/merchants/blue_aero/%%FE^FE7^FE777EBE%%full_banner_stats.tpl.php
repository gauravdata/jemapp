<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from full_banner_stats.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'full_banner_stats.tpl', 2, false),)), $this); ?>
<!-- full_banner_stats -->
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"bannersCount\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'banners.'), $this);?>

<div style="margin-left: 50px">
<?php echo "<div id=\"bannersCounts\" class=\"BannerCounts\"></div>"; ?>
</div>