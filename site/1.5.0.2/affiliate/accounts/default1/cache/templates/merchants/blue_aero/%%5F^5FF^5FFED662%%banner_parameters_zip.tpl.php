<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from banner_parameters_zip.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_parameters_zip.tpl', 3, false),)), $this); ?>
<!-- banner_parameters_zip -->
<fieldset class="BannerSite">
<legend><?php echo smarty_function_localize(array('str' => 'Banner settings'), $this);?>
</legend>
    <?php echo "<div id=\"zipFile\"></div>"; ?>
    <?php echo "<div id=\"fileTypes\"></div>"; ?>
    <div class="clear" style="height: 10px;"></div>
    
    <?php echo "<div id=\"bannerPreview\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"files\"></div>"; ?>