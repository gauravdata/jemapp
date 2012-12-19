<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:42
         compiled from wallpaper_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'wallpaper_settings.tpl', 4, false),)), $this); ?>
<!-- wallpaper_settings -->

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Select wallpaper'), $this);?>
</legend>
<div class="WallPaperSettings">
<?php echo "<div id=\"WallpapersPanel\"></div>"; ?>
</div>
<div class="clear"></div>
<?php echo "<div id=\"addCustomWallpaper\"></div>"; ?>
<div class="clear"></div>
</fieldset>

<fieldset class="WallpaperPositionSetting">
<legend><?php echo smarty_function_localize(array('str' => 'Wallpaper settings'), $this);?>
</legend>
<?php echo "<div id=\"wallpaperPosition\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Background Color'), $this);?>
</legend>
<?php echo "<div id=\"backgroundColor\"></div>"; ?>
</fieldset>

<div class="clear"></div>
<?php echo "<div id=\"save\"></div>"; ?>
<div class="clear"></div>