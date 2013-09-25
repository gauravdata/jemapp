<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from send_mass_mail.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'send_mass_mail.tpl', 5, false),)), $this); ?>
<!-- send_mass_mail -->

<div class="Recipients">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Recipients'), $this);?>
</legend>
<?php echo "<div id=\"affiliatesFilter\"></div>"; ?> 
<?php echo "<div id=\"defineCustomFilters\"></div>"; ?>
<?php echo "<div id=\"includeCustomMails\" class=\"CustomMailsFormfield\"></div>"; ?>    
</fieldset>
</div>
		
<div class="Email">	
<?php echo "<div id=\"emailForm\"></div>"; ?>
</div>
		
<?php echo "<div id=\"SaveButton\"></div>"; ?> <?php echo "<div id=\"ClearButton\"></div>"; ?> <?php echo "<div id=\"LoadTemplateButton\"></div>"; ?>