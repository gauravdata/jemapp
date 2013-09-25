<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:46
         compiled from wallpaper_add_custom.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'wallpaper_add_custom.tpl', 3, false),)), $this); ?>
<!-- wallpaper_add_custom -->
<fieldset class="WallpaperAddNew">
<legend><?php echo smarty_function_localize(array('str' => 'Add new wallpaper'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'If your wallpaper is saved on Internet/Intranet and is accessible from your browser, you can add it by entering URL of this wallpaper.'), $this);?>

<?php echo smarty_function_localize(array('str' => 'In this case will be wallpaper loaded always from URL you entered and not from your server.'), $this);?>
<br/>
<?php echo "<div id=\"CustomUrlPreview\"></div>"; ?><br/>
<?php echo smarty_function_localize(array('str' => 'Enter URL'), $this);?>
 <?php echo "<div id=\"CustomUrl\"></div>"; ?>
<?php echo "<div id=\"CustomUrlAdd\"></div>"; ?>
</fieldset>
<fieldset class="WallpaperAddNew">
<legend><?php echo smarty_function_localize(array('str' => 'Upload wallpaper'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Upload new wallpaper to your account. Wallpaper will be saved in your account and always loaded from server.'), $this);?>
<br/> 
<?php echo "<div id=\"UploadWallpaper\"></div>"; ?>
</fieldset>