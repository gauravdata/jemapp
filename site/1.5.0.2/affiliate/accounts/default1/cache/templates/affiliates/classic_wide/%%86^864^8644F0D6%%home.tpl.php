<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:41
         compiled from home.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'home.tpl', 11, false),)), $this); ?>
<!-- home -->
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td><?php echo "<div id=\"WelcomeMessage\" class=\"WelcomeMessage\"></div>"; ?></td>
  <td rowspan="2" width="100%" valign="top"><?php echo "<div id=\"AffiliateManager\"></div>"; ?></td>
</tr><tr>
  <td valign="top"><?php echo "<div id=\"RefreshButton\"></div>"; ?><?php echo "<div id=\"PeriodStats\"></div>"; ?></td>
</tr>
</table>

<div class="QuickNavigationIcons"><?php echo smarty_function_localize(array('str' => 'Quick Navigation Icons'), $this);?>
</div>
<div class="IconsPanel">
	<?php echo "<div id=\"Tutorial\"></div>"; ?>
	<?php echo "<div id=\"MyProfile\"></div>"; ?>
	<?php echo "<div id=\"Reports\"></div>"; ?>
	<?php echo "<div id=\"SignupSubaffiliates\"></div>"; ?>
	<?php echo "<div id=\"Campaigns\"></div>"; ?>
	<?php echo "<div id=\"Banners\"></div>"; ?>
	<?php echo "<div id=\"DirectLinks\"></div>"; ?>
</div>	
<div class="clear"></div>