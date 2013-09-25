<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from affiliate_delete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_delete.tpl', 4, false),)), $this); ?>
<!-- affiliate_delete -->
<table border=0 width="100%">
<tr>
  <td colspan="2" align="center" valign="top" style="height: 30px;"><?php echo smarty_function_localize(array('str' => 'Are you sure you want to delete selected affiliate(s)?'), $this);?>
</td>
</tr><tr>
  <td colspan="2" align="center" valign="top" style="height: 30px;">
    <?php echo smarty_function_localize(array('str' => 'What to do with child affiliates'), $this);?>
 
    <?php echo "<div id=\"MoveAffiliatesRadio\"></div>"; ?>
  </td>
</tr><tr>
  <td align="right"><?php echo "<div id=\"OkButton\"></div>"; ?></td>
  <td align="left"><?php echo "<div id=\"CancelButton\"></div>"; ?></td>
</tr>
</table>