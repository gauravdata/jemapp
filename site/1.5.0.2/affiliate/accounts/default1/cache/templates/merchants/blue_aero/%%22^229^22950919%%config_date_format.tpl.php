<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:28
         compiled from config_date_format.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'config_date_format.tpl', 2, false),)), $this); ?>
<!-- config_date_format -->
<legend><?php echo smarty_function_localize(array('str' => 'Date format'), $this);?>
</legend>
<?php echo "<div id=\"frameButton\"></div>"; ?>
<?php echo "<div id=\"frameButton1\"></div>"; ?>
<table>
    <tr>
        <td><?php echo "<div id=\"date_format_preset\"></div>"; ?></td>
        <td><?php echo "<div id=\"date_format\"></div>"; ?></td>
    </tr>
    <tr>
        <td><?php echo "<div id=\"time_format_preset\"></div>"; ?></td>
        <td><?php echo "<div id=\"time_format\"></div>"; ?></td>
    </tr>
</table>