<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:59
         compiled from generate_coupons_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'generate_coupons_form.tpl', 4, false),)), $this); ?>
<!--    generate_coupons_form   -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Generate coupons'), $this);?>
</legend>
    <?php echo "<div id=\"validfrom\"></div>"; ?>
    <?php echo "<div id=\"unlimitedValidity\"></div>"; ?>
    <?php echo "<div id=\"validto\"></div>"; ?>
    <?php echo "<div id=\"maxusecount\"></div>"; ?>
    <?php echo "<div id=\"couponsformat\"></div>"; ?>
    <?php echo "<div id=\"couponassigment\" class=\"CouponAssigment\"></div>"; ?>
    <?php echo "<div id=\"couponscount\"></div>"; ?>
    <?php echo "<div id=\"affiliates\"></div>"; ?>
    <?php echo "<div id=\"bannerid\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"message\"></div>"; ?>
<?php echo "<div id=\"saveButton\"></div>"; ?>