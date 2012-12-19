<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from affiliate_panel_config.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_panel_config.tpl', 3, false),)), $this); ?>
<!-- affiliate_panel_config -->

<h3 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'Affiliate Panel menu & screens'), $this);?>
</h3>
<?php echo smarty_function_localize(array('str' => 'You have full control over the look of your affiliate panel - you can customize the menu, and even add your static custom pages.'), $this);?>

<br/><br/>
<?php echo "<div id=\"LayoutPanel\" class=\"AffiliatePanel\"></div>"; ?>