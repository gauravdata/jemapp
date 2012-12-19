<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from campaign_form_show.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'campaign_form_show.tpl', 9, false),)), $this); ?>
<!-- campaign_form_show -->
<div class="ScreenHeader CampaignViewHeader">
	<div class="CampaignLogo"><?php echo "<div id=\"logo\"></div>"; ?></div>
	<?php echo "<div id=\"RefreshButton\"></div>"; ?>
	<div class="ScreenTitle"><?php echo "<div id=\"name\"></div>"; ?></div>
	<div class="ScreenDescription">
	   <?php echo "<div id=\"description\"></div>"; ?>
	   <br/>
	   <?php echo smarty_function_localize(array('str' => 'Campaign is '), $this);?>
 <b><?php echo "<div id=\"rstatus\"></div>"; ?></b>
	</div>
	<div class="clear"/>
</div>