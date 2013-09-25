<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:50
         compiled from custom_date_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'custom_date_panel.tpl', 4, false),)), $this); ?>
<!-- custom_date_panel -->
<table>
    <tr>
        <td><div class="MarginRight"><?php echo smarty_function_localize(array('str' => 'from'), $this);?>
</div></td> 
        <td><?php echo "<div id=\"from\"></div>"; ?></td>
        <td><div class="MarginBoth"><?php echo smarty_function_localize(array('str' => 'to'), $this);?>
</div></td> 
        <td><?php echo "<div id=\"to\"></div>"; ?></td>
    </tr>
</table>