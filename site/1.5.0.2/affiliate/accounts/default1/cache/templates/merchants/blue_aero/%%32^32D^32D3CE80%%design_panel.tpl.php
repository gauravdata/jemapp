<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:38
         compiled from design_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'design_panel.tpl', 8, false),)), $this); ?>
<!--    design_panel    -->

<?php echo "<div id=\"size\"></div>"; ?>
<?php echo "<div id=\"rtype\"></div>"; ?>
<?php echo "<div id=\"isdesign\"></div>"; ?>
<?php echo "<div id=\"data3\"></div>"; ?>
<div class="FormField">
    <div class="FormFieldLabel"><?php echo smarty_function_localize(array('str' => 'Coupon preview'), $this);?>
</div>
    <div class="FormFieldInputContainer"><?php echo "<div id=\"preview\"></div>"; ?></div>
    <div class="clear"></div>    
</div>
<div style="clear: both"></div>
<?php echo "<div id=\"message\"></div>"; ?>
<?php echo "<div id=\"saveButton\"></div>"; ?>