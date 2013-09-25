<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:14
         compiled from imagepicker.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'imagepicker.tpl', 8, false),)), $this); ?>
<!-- imagepicker -->
<div class="PopupWinTopLeft"><div class="PopupWinTopRight"><div class="PopupWinTop"></div></div></div>
<div class="PopupWinLeft"><div class="PopupWinRight">
<div class="PopupWinMain">

	<table border=0 cellspacing=0 cellpadding="3">
	<tr>
	  <td valign="top" colspan="3"><?php echo smarty_function_localize(array('str' => 'Upload image'), $this);?>
</td>
	</tr><tr>
	  <td valign="top"><?php echo "<div id=\"FileUpload\"></div>"; ?></td>
	  <td>&nbsp;</td>
	  <td><?php echo "<div id=\"UploadApplyButton\"></div>"; ?></td>
	</tr><tr>
	  <td valign="top" align="left" colspan="3"><?php echo smarty_function_localize(array('str' => 'File on the Internet (enter image URL)'), $this);?>
</td>
	</tr><tr>
	  <td valign="top" align="left" ><?php echo "<div id=\"FileUrl\"></div>"; ?></td>
	  <td>&nbsp;</td>
	  <td><?php echo "<div id=\"UrlApplyButton\"></div>"; ?></td>
	</table>
	
</div></div></div>
<div class="PopupWinBottomLeft"><div class="PopupWinBottomRight"><div class="PopupWinBottom"></div></div></div>