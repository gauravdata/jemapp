<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from transaction_info.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'transaction_info.tpl', 4, false),)), $this); ?>
<!-- transaction_info -->

<?php echo "<div id=\"transid\"></div>"; ?>
<div class="Inliner" style="width: 95px;"><?php echo smarty_function_localize(array('str' => 'Affiliate'), $this);?>
</div>
<div class="Inliner"><?php echo "<div id=\"userid\"></div>"; ?></div>
<div class="Inliner"><?php echo "<div id=\"firstname\"></div>"; ?></div>
<div class="Inliner"><?php echo "<div id=\"lastname\"></div>"; ?></div>
<div class="Inliner">(<?php echo "<div id=\"username\"></div>"; ?>)</div>
<div class="clear"></div>

<?php echo "<div id=\"campaignname\"></div>"; ?>

<div class="Inliner TransactionData"><?php echo "<div id=\"dateinserted\"></div>"; ?></div>
<div class="Inliner"><?php echo "<div id=\"data1\"></div>"; ?></div>
<div class="clear"></div>
<div class="Inliner TransactionData"><?php echo "<div id=\"commission\"></div>"; ?></div>
<div class="Inliner"><?php echo "<div id=\"data2\"></div>"; ?></div>
<div class="clear"></div>
<div class="Inliner TransactionData"><?php echo "<div id=\"rtype\"></div>"; ?></div>
<div class="Inliner"><?php echo "<div id=\"data3\"></div>"; ?></div>
<div class="clear"></div>
<div class="Inliner TransactionData"><?php echo "<div id=\"rstatus\"></div>"; ?></div>
<div class="Inliner"><?php echo "<div id=\"data4\"></div>"; ?></div>
<div class="clear"></div>
<div class="Inliner TransactionData"><?php echo "<div id=\"payoutstatus\"></div>"; ?></div>
<div class="Inliner"><?php echo "<div id=\"data5\"></div>"; ?></div>
<div class="clear"></div>

<?php echo "<div id=\"refererurl\"></div>"; ?>
<?php echo "<div id=\"ip\"></div>"; ?>
<?php echo "<div id=\"trackmethod\"></div>"; ?>
<?php echo "<div id=\"visitorid\"></div>"; ?>
<br/>
<div style="font-weight: bold; text-align: center"><?php echo smarty_function_localize(array('str' => 'Very first click'), $this);?>
</div>
<?php echo "<div id=\"firstclicktime\"></div>"; ?>
<?php echo "<div id=\"firstclickreferer\"></div>"; ?>
<?php echo "<div id=\"firstclickip\"></div>"; ?>
<?php echo "<div id=\"firstclickdata1\"></div>"; ?>
<?php echo "<div id=\"firstclickdata2\"></div>"; ?>
<br/>
<div style="font-weight: bold; text-align: center"><?php echo smarty_function_localize(array('str' => 'Very last click'), $this);?>
</div>
<?php echo "<div id=\"lastclicktime\"></div>"; ?>
<?php echo "<div id=\"lastclickreferer\"></div>"; ?>
<?php echo "<div id=\"lastclickip\"></div>"; ?>
<?php echo "<div id=\"lastclickdata1\"></div>"; ?>
<?php echo "<div id=\"lastclickdata2\"></div>"; ?>

<div class="clear"></div>