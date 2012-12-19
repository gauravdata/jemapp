<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from banner_details.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_details.tpl', 5, false),)), $this); ?>
<!-- banner_details -->

<div class="GeneralBannerInfo">
<fieldset>
	<legend><?php echo smarty_function_localize(array('str' => 'General Banner Information'), $this);?>
</legend>
	<?php echo "<div id=\"rtype\"></div>"; ?>
	<?php echo "<div id=\"name\"></div>"; ?>
	<?php echo "<div id=\"rstatus\"></div>"; ?>
	<?php echo "<div id=\"campaignid\"></div>"; ?>
	<?php echo "<div id=\"description\"></div>"; ?>
	<?php echo "<div id=\"size\" class=\"Size\"></div>"; ?>
	<?php echo "<div id=\"data4\"></div>"; ?>
	<?php echo "<div id=\"seostring\"></div>"; ?>
</fieldset>
</div>

<?php echo "<div id=\"BannerDestination\"></div>"; ?>

<?php echo "<div id=\"Parameters\"></div>"; ?>
<?php echo "<div id=\"ExtendedDescription\"></div>"; ?> 
<?php echo "<div id=\"FormMessage\"></div>"; ?>

<div class="ButtonSet"><?php echo "<div id=\"SaveButton\"></div>"; ?></div>

<br/><br/>
