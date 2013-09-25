<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from coupon_view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'coupon_view.tpl', 3, false),)), $this); ?>
<!--    coupon_view     -->

<b><?php echo smarty_function_localize(array('str' => 'Description of coupon:'), $this);?>
</b><br/>
<?php echo "<div id=\"description\"></div>"; ?><br/><br/>
<b><?php echo smarty_function_localize(array('str' => 'Coupons:'), $this);?>
</b>
<?php echo "<div id=\"coupons\"></div>"; ?>