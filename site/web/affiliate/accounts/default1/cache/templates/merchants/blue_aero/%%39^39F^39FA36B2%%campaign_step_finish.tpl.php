<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:27
         compiled from campaign_step_finish.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'campaign_step_finish.tpl', 4, false),)), $this); ?>
<!-- campaign_step_finish -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "commissions_details_view.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<b><?php echo smarty_function_localize(array('str' => 'Your campaign was succesfuly defined'), $this);?>
</b>
<?php echo smarty_function_localize(array('str' => 'This campaign is still not saved and not active. 
If you completed the configuration, you can finish it using the button below.
Finization means that the campaign will be save and becomes active in the system.'), $this);?>


<?php echo "<div id=\"SaveCampaignButton\"></div>"; ?>

<i><?php echo smarty_function_localize(array('str' => 'If you don\'t save the campaign, all the data you entered in this wizard will be lost'), $this);?>
</i>