<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:35
         compiled from promotools_screen.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'promotools_screen.tpl', 10, false),)), $this); ?>
<!-- promotools_screen -->

<?php echo "<div id=\"Campaigns\"></div>"; ?>
<?php echo "<div id=\"promoMaterials\"></div>"; ?>
<?php echo "<div id=\"Channels\"></div>"; ?>

<div style="clear: both"></div>

<br/><br />
<strong><?php echo smarty_function_localize(array('str' => 'Advanced Functionality'), $this);?>
</strong>
<div class="LineShort"></div>
<br/>
<?php echo "<div id=\"AffLinkProtector\"></div>"; ?>
<?php echo "<div id=\"SignupSubaffiliates\"></div>"; ?>
<?php echo "<div id=\"SubIdTracking\"></div>"; ?>