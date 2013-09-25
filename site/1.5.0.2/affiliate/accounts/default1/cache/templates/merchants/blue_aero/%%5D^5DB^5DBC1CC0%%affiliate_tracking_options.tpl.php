<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from affiliate_tracking_options.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_tracking_options.tpl', 5, false),)), $this); ?>
<!-- affiliate_tracking_options -->
<table class="AffiliateTrackingOptions">
<tr><td valign="top">
        <fieldset>
            <legend><?php echo smarty_function_localize(array('str' => 'Cookie settings'), $this);?>
</legend>
            <div class="HintText"><?php echo smarty_function_localize(array('str' => 'This setting can override the default configuration from campaign'), $this);?>
</div> 
            <?php echo "<div id=\"overwriteCookie\"></div>"; ?>
            <?php echo "<div id=\"FormMessage\"></div>"; ?>
            <?php echo "<div id=\"SaveButton\"></div>"; ?>
        </fieldset>
    </td><td valign="top">
        <fieldset>
            <legend><?php echo smarty_function_localize(array('str' => 'Test link'), $this);?>
</legend>
            <?php echo "<div id=\"TestLinkPanel\"></div>"; ?>
        </fieldset>
    </td></tr>
</table>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'DirectLink URLs'), $this);?>
</legend>
    <?php echo "<div id=\"affiliateUrlsGrid\"></div>"; ?>
</fieldset>