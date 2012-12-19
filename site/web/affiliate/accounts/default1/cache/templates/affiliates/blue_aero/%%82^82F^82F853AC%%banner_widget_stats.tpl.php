<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:51
         compiled from banner_widget_stats.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_widget_stats.tpl', 4, false),)), $this); ?>
<!--    banner_widget_stats   -->
<table width="100%" class="BannerTableStats" cellpadding="2" cellspacing="2">
  <tr >
<td colspan="8" align="center" class="white" > <?php echo smarty_function_localize(array('str' => 'Banner stats'), $this);?>
 <?php echo "<div id=\"forChannel\" class=\"Inline\"></div>"; ?></td>
</tr><tr>

<td width="16%" align="left"><?php echo smarty_function_localize(array('str' => 'Impressions:'), $this);?>
 </td>
<td style="color: #000000" width="16%" align="right"><?php echo "<div id=\"impsLabel\" class=\"Inline\"></div>"; ?></td>

<td width="16%" align="left"><?php echo smarty_function_localize(array('str' => 'Clicks:'), $this);?>
 </td>
<td style="color: #000000" width="16%" align="right"><?php echo "<div id=\"clicksLabel\" class=\"Inline\"></div>"; ?></td>

<td width="16%" align="left"><?php echo smarty_function_localize(array('str' => 'CTR:'), $this);?>
 </td>
<td style="color: #000000" width="16%" align="right"><?php echo "<div id=\"ctrLabel\" class=\"Inline\"></div>"; ?></td>

</tr><tr>

<td width="16%" align="left"><?php echo smarty_function_localize(array('str' => 'Sales:'), $this);?>
 </td>
<td style="color: #000000" width="16%" align="right"><?php echo "<div id=\"salesLabel\" class=\"Inline\"></div>"; ?></td>

<td width="16%" align="left"><?php echo smarty_function_localize(array('str' => 'Commissions:'), $this);?>
 </td>
<td style="color: #000000" width="16%" align="right"><?php echo "<div id=\"commLabel\" class=\"Inline\"></div>"; ?></td>

<td width="16%" align="left" nowrap><?php echo smarty_function_localize(array('str' => 'SCR (sales / clicks):'), $this);?>
 </td>
<td style="color: #000000" width="16%" align="right"><?php echo "<div id=\"scrLabel\" class=\"Inline\"></div>"; ?></td>

</tr>
</table>