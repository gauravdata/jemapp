<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:53
         compiled from channel_stats_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'channel_stats_filter.tpl', 6, false),)), $this); ?>
<!-- channel_stats_filter -->

<div class="ReportsFilter">
    <div class="ColumnFieldset">
        <fieldset class="Filter FilterCampaign">
            <legend><?php echo smarty_function_localize(array('str' => 'Campaign'), $this);?>
</legend>
            <div class="Resize">
                <?php echo "<div id=\"campaignid\"></div>"; ?>
            </div>
        </fieldset>
    </div>
    
    <fieldset class="Filter FilterDate">
        <legend><?php echo smarty_function_localize(array('str' => 'Date'), $this);?>
</legend>
        <div class="Resize">
            <?php echo "<div id=\"datetime\"></div>"; ?>
        </div>
    </fieldset>  
</div>
<div style="clear: both;"></div>