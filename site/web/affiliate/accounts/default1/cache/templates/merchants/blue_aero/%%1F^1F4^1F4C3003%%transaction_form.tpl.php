<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from transaction_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'transaction_form.tpl', 3, false),)), $this); ?>
<!-- transaction_form -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Transaction'), $this);?>
</legend>
<?php echo "<div id=\"rstatus\"></div>"; ?>
<?php echo "<div id=\"rtype\"></div>"; ?>
<?php echo "<div id=\"payoutstatus\"></div>"; ?>
<?php echo "<div id=\"totalcost\"></div>"; ?>
<?php echo "<div id=\"refererurl\"></div>"; ?>
<?php echo "<div id=\"userid\"></div>"; ?>
<?php echo "<div id=\"commission\"></div>"; ?>
<?php echo "<div id=\"ip\"></div>"; ?>
<?php echo "<div id=\"productid\"></div>"; ?>
<?php echo "<div id=\"data1\"></div>"; ?>
<?php echo "<div id=\"data2\"></div>"; ?>
<?php echo "<div id=\"data3\"></div>"; ?>
<?php echo "<div id=\"data4\"></div>"; ?>
<?php echo "<div id=\"data5\"></div>"; ?>
</fieldset>
<table>
    <tbody>
        <tr>
            <td><?php echo "<div id=\"SaveButton\"></div>"; ?></td>
            <td><?php echo "<div id=\"CancelButton\"></div>"; ?></td>
        </tr>
    </tbody>
</table>