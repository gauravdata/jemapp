<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from import_coupons_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'import_coupons_form.tpl', 4, false),)), $this); ?>
<!--    import_coupons_form     -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Import coupons'), $this);?>
</legend>
    <?php echo "<div id=\"validfrom\"></div>"; ?>
    <?php echo "<div id=\"unlimitedValidity\"></div>"; ?>
    <?php echo "<div id=\"validto\"></div>"; ?>
    <?php echo "<div id=\"maxusecount\"></div>"; ?>
    <?php echo "<div id=\"couponcodes\"></div>"; ?>
    <?php echo "<div id=\"couponassigment\" class=\"CouponAssigment\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"message\"></div>"; ?>
<?php echo "<div id=\"saveButton\"></div>"; ?>