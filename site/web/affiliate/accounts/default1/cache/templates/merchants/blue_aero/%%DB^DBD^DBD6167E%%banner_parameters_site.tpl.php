<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from banner_parameters_site.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_parameters_site.tpl', 3, false),)), $this); ?>
<!-- banner_parameters_site -->
<fieldset class="BannerSite">
<legend><?php echo smarty_function_localize(array('str' => 'Replicated site URL'), $this);?>
</legend>
    <?php echo "<div id=\"destinationurl\" class=\"DestinationUrl\"></div>"; ?>
    <div class="clear" style="height: 10px;"></div>
    
    <div class="FormFieldLabel"><div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Preview for'), $this);?>
</div></div>
    <div class="FormFieldInputContainer">
        <div class="FormFieldInput AffiliateInput"><?php echo "<div id=\"affiliate\"></div>"; ?></div>
        <div class="Inline"><?php echo "<div id=\"previewLink\"></div>"; ?></div>
    </div>
</fieldset>

<?php echo "<div id=\"files\"></div>"; ?>

<fieldset class="BannerSite">
<legend><?php echo smarty_function_localize(array('str' => 'Aditional options'), $this);?>
</legend>
    <?php echo "<div id=\"encode\"></div>"; ?>
</fieldset>