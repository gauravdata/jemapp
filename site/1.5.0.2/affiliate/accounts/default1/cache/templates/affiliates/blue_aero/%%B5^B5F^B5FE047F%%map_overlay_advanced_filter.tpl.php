<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:21
         compiled from map_overlay_advanced_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'map_overlay_advanced_filter.tpl', 4, false),)), $this); ?>
<!-- map_overlay_advanced_filter -->
<div>
    <fieldset class="Filter">
        <legend><?php echo smarty_function_localize(array('str' => 'Statistics date range'), $this);?>
</legend>
        <div class="Resize"><?php echo "<div id=\"datetime\"></div>"; ?></div>
    </fieldset>
    <fieldset class="Filter MapOverlayFilterCampaign">
        <legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
        <div class="Resize"><?php echo "<div id=\"campaignid\"></div>"; ?></div>
    </fieldset>
    <fieldset class="Filter MapOverlayFilterAffiliate">
        <legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
        <div class="Resize"><?php echo "<div id=\"rstatus\"></div>"; ?></div>
    </fieldset>
 </div>
<div style="clear: both;"></div>