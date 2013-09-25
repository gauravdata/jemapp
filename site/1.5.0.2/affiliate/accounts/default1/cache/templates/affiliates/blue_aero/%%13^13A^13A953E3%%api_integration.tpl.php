<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:51
         compiled from api_integration.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'api_integration.tpl', 3, false),)), $this); ?>
<!-- api_integration -->

<h4><?php echo smarty_function_localize(array('str' => 'Download of DpApi.class.php file'), $this);?>
</h4>
<?php echo smarty_function_localize(array('str' => 'PapApi.class.php is bundled to each distribution of DownloadProtect'), $this);?>
.
##Because of this you should update your DpApi.class.php file each time your DownloadProtect is upadted.
<?php echo "<div id=\"APIDownloadLink\"></div>"; ?>