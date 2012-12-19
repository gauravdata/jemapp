<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from tracking_tab.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'tracking_tab.tpl', 6, false),)), $this); ?>
<!-- tracking_tab -->

<div class="TrackingSettingsForm">

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'URLs'), $this);?>
</legend>
    <?php echo "<div id=\"mainSiteUrl\" class=\"MainSiteUrl\"></div>"; ?>
</fieldset>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Clicks'), $this);?>
</legend>
    <div class="Inliner"><div class="Label"><?php echo smarty_function_localize(array('str' => 'Delete raw click records older than'), $this);?>
</div></div>
    <div class="FormFieldSmallInline"><?php echo "<div id=\"deleterawclicks\"></div>"; ?></div><div class="Inliner"><?php echo smarty_function_localize(array('str' => 'days'), $this);?>
</div>
    <div class="clear"></div>
</fieldset>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Tracking'), $this);?>
</legend>
    <?php echo "<div id=\"track_by_ip\"></div>"; ?>
    <?php echo "<div id=\"ip_validity\" class=\"IpValidity\"></div>"; ?>
    <?php echo "<div id=\"ip_validity_format\" class=\"Validity\"></div>"; ?>
    <div class="Line"></div>
    <?php echo "<div id=\"save_unrefered_sale_lead\" class=\"SaveUnrefered\"></div>"; ?>
    <?php echo "<div id=\"default_affiliate\"></div>"; ?>
    <div class="Line"></div>
    <?php echo "<div id=\"force_choosing_productid\"></div>"; ?>
    <div class="Line"></div>
    <?php echo "<div id=\"deleteExpiredVisitors\"></div>"; ?>
    <div class="Line"></div>
    <?php echo "<div id=\"allowComputeNegativeCommission\"></div>"; ?>
</fieldset>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Affiliate linking method'), $this);?>
</legend>
    <?php echo smarty_function_localize(array('str' => 'This will set the style of affiliate link URL that your affiliates will put to their pages. You can choose from the methods below.<br/>Note that some methods have different requirements'), $this);?>

    
    <?php echo "<div id=\"linking_method\" class=\"LinkingMethod\"></div>"; ?>
    
    
    <div class="Line"></div>
    <div class="HintText"><?php echo smarty_function_localize(array('str' => 'You can choose to support DirectLink linking as an addition to your standard affiliate links.<br/>The links chosen above will work, plus your affiliates will have option to use DirectLinks. All affiliate DirectLink URLs require merchant\'s approval.'), $this);?>
</div>
    <div class="Line"></div>
    
    <?php echo "<div id=\"support_direct_linking\" class=\"SupportDirect\"></div>"; ?>
    
    <?php echo "<div id=\"support_short_anchor_linking\"></div>"; ?>
</fieldset>

</div>
<?php echo "<div id=\"SaveButton\"></div>"; ?>

<div class="clear"></div>