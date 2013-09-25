<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:42
         compiled from payout_data.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'payout_data.tpl', 11, false),)), $this); ?>
<!-- payout_data -->
<div class="GadgetInTopLeft">
<div class="GadgetInTopRight">
<div class="GadgetInTop">
</div></div></div>

<div class="GadgetInLeft"><div class="GadgetInRight">
<div class="GadgetInMain">
<table>
    <tr>
        <td><div class="GadgetInTitle"><?php echo smarty_function_localize(array('str' => 'Payout'), $this);?>
</div></td>
        <td></td>
    </tr>
    <tr>
        <td><?php echo smarty_function_localize(array('str' => 'Your total balance: '), $this);?>
</td>
        <td><?php echo "<div id=\"approvedCommissions\"></div>"; ?></td>
    </tr>
    <tr>
        <td><?php echo smarty_function_localize(array('str' => 'Pending commissions: '), $this);?>
</td>
        <td><?php echo "<div id=\"pendingCommissions\"></div>"; ?></td>
    </tr>
    <tr>
        <td colspan="2"><?php echo "<div id=\"payoutsHistoryLink\"></div>"; ?></td>
    </tr>
</table>
</div>
</div></div>

<div class="GadgetInBottomLeft">
<div class="GadgetInBottomRight">
<div class="GadgetInBottom">
</div></div></div>