<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:14
         compiled from ip_detail.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'ip_detail.tpl', 7, false),)), $this); ?>
<!-- ip_detail -->
<div class="PopupWinTopLeft"><div class="PopupWinTopRight"><div class="PopupWinTop"></div></div></div>
<div class="PopupWinLeft"><div class="PopupWinRight">
<div class="PopupWinMain">
<?php echo "<div id=\"flag\" class=\"IpLocationFlag\"></div>"; ?>
<div class="IpDetailContent">
	<b><?php echo smarty_function_localize(array('str' => 'Ip address'), $this);?>
:</b> <?php echo "<div id=\"ip\"></div>"; ?>
	<div class="clear"></div>
	<b><?php echo smarty_function_localize(array('str' => 'Country'), $this);?>
:</b>&nbsp;<?php echo "<div id=\"countryName\"></div>"; ?>&nbsp;-&nbsp;<?php echo "<div id=\"countryCode\"></div>"; ?>
	<div class="clear"></div>
	<b><?php echo smarty_function_localize(array('str' => 'City'), $this);?>
:</b>&nbsp;<?php echo "<div id=\"city\"></div>"; ?>&nbsp;<?php echo "<div id=\"region\"></div>"; ?>&nbsp;<?php echo "<div id=\"postalCode\"></div>"; ?>
	<div class="clear"></div>
	<b><?php echo smarty_function_localize(array('str' => 'Geo Location'), $this);?>
:</b>&nbsp;<?php echo "<div id=\"latitude\"></div>"; ?>,&nbsp;<?php echo "<div id=\"longitude\"></div>"; ?> 
	<div class="clear"></div>
</div>
<div class="clear"></div>

<?php echo "<div id=\"Extensions\"></div>"; ?>
<div class="clear"></div>
</div></div></div>
<div class="PopupWinBottomLeft"><div class="PopupWinBottomRight"><div class="PopupWinBottom"></div></div></div>
    