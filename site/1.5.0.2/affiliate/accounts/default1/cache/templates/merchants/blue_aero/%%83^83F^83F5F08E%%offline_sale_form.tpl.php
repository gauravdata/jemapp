<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from offline_sale_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'offline_sale_form.tpl', 4, false),)), $this); ?>
<!--    offline_sale_form   -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Coupon details'), $this);?>
</legend>
    <?php echo "<div id=\"couponcode\"></div>"; ?>
    <?php echo "<div id=\"name\"></div>"; ?>
    <?php echo "<div id=\"description\"></div>"; ?>
    <?php echo "<div id=\"userid\"></div>"; ?>
</fieldset>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Sale'), $this);?>
</legend>
    <?php echo "<div id=\"totalcost\"></div>"; ?>
    <?php echo "<div id=\"orderid\"></div>"; ?>
    <?php echo "<div id=\"productid\"></div>"; ?>
    <?php echo "<div id=\"data1\"></div>"; ?>
    <?php echo "<div id=\"data2\"></div>"; ?>
    <?php echo "<div id=\"data3\"></div>"; ?>
    <?php echo "<div id=\"data4\"></div>"; ?>
    <?php echo "<div id=\"data5\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"message\"></div>"; ?>
<?php echo "<div id=\"saleButton\"></div>"; ?>