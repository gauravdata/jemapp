<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from banner_preview_field.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_preview_field.tpl', 5, false),)), $this); ?>
<!--    banner_preview_field   -->

<div class="Preview">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Preview'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'You can preview rebranded PDF document - same result will see your affiliates. Variables will be replaced with values related to selected affiliate from list box.'), $this);?>
<br/>
<div style="width: 7em; vertical-align: middle; float: left; padding-top: 0.3em;"><?php echo smarty_function_localize(array('str' => 'Affiliate'), $this);?>
</div><?php echo "<div id=\"affiliateBox\"></div>"; ?>    
<?php echo "<div id=\"previewButton\"></div>"; ?>
<?php echo "<div id=\"infoLabel\"></div>"; ?>
</fieldset>
</div>