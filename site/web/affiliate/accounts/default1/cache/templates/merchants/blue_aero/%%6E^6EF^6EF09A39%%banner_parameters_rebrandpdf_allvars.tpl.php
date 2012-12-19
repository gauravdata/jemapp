<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from banner_parameters_rebrandpdf_allvars.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_parameters_rebrandpdf_allvars.tpl', 4, false),)), $this); ?>
<!-- banner_parameters_rebrandpdf_allvars -->

<fieldset style="width:500px;">
<legend><?php echo smarty_function_localize(array('str' => 'Supported Variables'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Below is list of supported variables you can use in your PDF document.
 Variables will be replaced by values related to affiliate, under which is PDF downloaded.
  Meaning of fields data1 - data10 you can customize in menu Configuration -> Affiliate Signup -> tab Fields'), $this);?>

  <br/><br/>
<?php echo "<div id=\"variableList\"></div>"; ?>
</fieldset>