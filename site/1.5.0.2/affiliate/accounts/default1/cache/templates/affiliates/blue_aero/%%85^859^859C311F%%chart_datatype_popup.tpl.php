<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from chart_datatype_popup.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'chart_datatype_popup.tpl', 8, false),)), $this); ?>
<!-- chart_datatype_popup -->
<div class="PopupWinTopLeft"><div class="PopupWinTopRight"><div class="PopupWinTop"></div></div></div>
<div class="PopupWinLeft"><div class="PopupWinRight">
<div class="PopupWinMain">

	<table border=0 cellspacing=0 cellpadding="3">
		<tr>
		  <td align="left"><?php echo smarty_function_localize(array('str' => 'First data line'), $this);?>
</td><td><?php echo "<div id=\"DataType1\"></div>"; ?></td>
		</tr>
		<tr>
		  <td align="left"><?php echo smarty_function_localize(array('str' => 'Second data line'), $this);?>
</td><td><?php echo "<div id=\"DataType2\"></div>"; ?></td>
		</tr>
		<tr>
		  <td class="Error" colspan="2"><?php echo "<div id=\"Error\"></div>"; ?></td>
		</tr>
		<tr>
		  <td colspan="2"><?php echo "<div id=\"ButtonApply\"></div>"; ?></td>
		</tr>
	</table>
	
</div></div></div>
<div class="PopupWinBottomLeft"><div class="PopupWinBottomRight"><div class="PopupWinBottom"></div></div></div>