<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:03
         compiled from coupon_previews.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'coupon_previews.tpl', 3, false),)), $this); ?>
<!--    coupon_previews     -->

<?php echo smarty_function_localize(array('str' => 'You have assigned'), $this);?>
 <?php echo "<div id=\"usedcoupons\"></div>"; ?> <?php echo smarty_function_localize(array('str' => 'coupons'), $this);?>
 (<?php echo "<div id=\"validcoupons\"></div>"; ?> <?php echo smarty_function_localize(array('str' => 'coupons of them are valid and not exhausted'), $this);?>
). <?php echo smarty_function_localize(array('str' => 'There are'), $this);?>
 <?php echo "<div id=\"availablecoupons\"></div>"; ?> <?php echo smarty_function_localize(array('str' => 'more available coupons'), $this);?>
 <?php echo "<div id=\"getCouponButton\"></div>"; ?>
<?php echo "<div id=\"exportCouponsButton\"></div>"; ?>
<div style="clear: both"></div>

<?php echo "<div id=\"couponsPreviewGrid\"></div>"; ?>

<div class="InlineText"><?php echo smarty_function_localize(array('str' => 'Print'), $this);?>
</div><div class="InlineText"><?php echo "<div id=\"copy\"></div>"; ?></div><div class="InlineText"><?php echo smarty_function_localize(array('str' => 'copies of coupons'), $this);?>
</div><?php echo "<div id=\"printButton\"></div>"; ?>