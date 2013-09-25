<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:52
         compiled from main_header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'main_header.tpl', 5, false),)), $this); ?>
<!-- main_header -->
<?php echo "<div id=\"Breadcrumbs\"></div>"; ?>
<div class="GeneralAffiliateLink">
<div class="FloatLeft"><?php echo "<div id=\"generalAffiliateLink\"></div>"; ?></div>
<div class="FloatRight"><?php echo smarty_function_localize(array('str' => 'Unpaid commissions (approved / pending):'), $this);?>
 <?php echo "<div id=\"totalCommisonsApprovedUnpaid\"></div>"; ?> / <?php echo "<div id=\"totalCommissionsPending\"></div>"; ?></div>
</div>
<div class="ClearLeft"></div>