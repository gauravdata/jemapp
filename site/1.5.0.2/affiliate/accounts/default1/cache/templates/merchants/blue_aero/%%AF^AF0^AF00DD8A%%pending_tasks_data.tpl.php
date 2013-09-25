<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:45
         compiled from pending_tasks_data.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'pending_tasks_data.tpl', 4, false),)), $this); ?>
<!-- pending_tasks_data  -->
<table>
<tr>
  <td colspan="3" style="color: orange; text-align: center;"><?php echo smarty_function_localize(array('str' => 'Tasks waiting for your approval:'), $this);?>
</td>
</tr><tr>
  <td><?php echo smarty_function_localize(array('str' => 'Affiliates'), $this);?>
</td><td>&nbsp;</td><td nowrap align="right"><?php echo "<div id=\"pendingAffiliates\"></div>"; ?></td>
</tr><tr>  
  <td><?php echo smarty_function_localize(array('str' => 'DirectLink URLs'), $this);?>
</td><td>&nbsp;</td><td nowrap align="right"><?php echo "<div id=\"pendingDirectLinks\"></div>"; ?></td>
</tr><tr>
  <td><?php echo smarty_function_localize(array('str' => 'Commissions'), $this);?>
</td><td>&nbsp;</td><td nowrap align="right"><?php echo "<div id=\"pendingCommissions\"></div>"; ?></td>
</tr><tr>
  <td>&nbsp;&nbsp;<?php echo smarty_function_localize(array('str' => 'of total sales value'), $this);?>
</td><td>&nbsp;</td><td nowrap align="right"><?php echo "<div id=\"totalCommissions\"></div>"; ?></td>
</tr><tr>
  <td><?php echo smarty_function_localize(array('str' => 'Unsent emails'), $this);?>
</td><td>&nbsp;</td><td nowrap align="right"><?php echo "<div id=\"unsentEmails\"></div>"; ?></td>
</tr>
</table>


