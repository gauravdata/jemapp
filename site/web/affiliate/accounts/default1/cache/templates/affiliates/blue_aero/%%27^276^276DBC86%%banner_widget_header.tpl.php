<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:51
         compiled from banner_widget_header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_widget_header.tpl', 7, false),)), $this); ?>
<!--    banner_widget_header   -->
<table class="BannerHeader" width="100%" padding="2" style="margin-left: 8px;">
<tr>
  <td style="color: #000000" align="left" colspan="2">
  	<div class="BannerTypeLabel"><?php echo "<div id=\"bannerTypeLabel\"></div>"; ?></div>
  </td>
  <td><?php echo smarty_function_localize(array('str' => 'Banner name: '), $this);?>
</td>
  <td align="left" style="color: #000000"><?php echo "<div id=\"bannerNameLabel\" class=\"Inline\"></div>"; ?></td>
</tr><tr>
  <td><?php echo smarty_function_localize(array('str' => 'Campaign: '), $this);?>
</td>
  <td align="left" style="color: #000000"><?php echo "<div id=\"campaignNameLabel\" class=\"Inline\"></div>"; ?></td>

  <td align="left"><?php echo smarty_function_localize(array('str' => 'Target: '), $this);?>
</td>
  <td style="color: #000000" align="left"><?php echo "<div id=\"bannerTargetUrlLabel\" class=\"Inline\"></div>"; ?></td>
</tr>
<tr>
  <td><?php echo smarty_function_localize(array('str' => 'Commissions: '), $this);?>
</td>
  <td style="color: #000000" align="left" colspan="3"><?php echo "<div id=\"campaignDetailsLabel\" class=\"Inline\"></div>"; ?></td>
</tr>
</table>