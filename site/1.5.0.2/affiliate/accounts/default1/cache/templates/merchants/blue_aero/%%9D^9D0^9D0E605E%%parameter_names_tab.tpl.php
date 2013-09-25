<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from parameter_names_tab.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'parameter_names_tab.tpl', 2, false),)), $this); ?>
<!-- parameter_names_tab -->
<h3 class="TabDescription"><?php echo smarty_function_localize(array('str' => 'URL Parameter Names'), $this);?>
</h3>
    <?php echo smarty_function_localize(array('str' => 'Here you can change the names of tracking parameters that are used in affiliate URLs.
    For example, instead of www.yoursite.com/?<strong>a_aid</strong>=12345<br/>
    you can have www.yoursite.com/?<strong>ref</strong>=12345 or any name of your choice.<br/>
    Note: If you change parameter names, you should tell your affiliates to change their existing links because old parameter names will not work.
	<br/>
    It is recommended to keep default parameter names if you don\'t have any reason to change it.'), $this);?>


<div class="ParameterNamesForm">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Parameter names'), $this);?>
</legend>
<?php echo "<div id=\"parameterNamesForm\"></div>"; ?>
</fieldset>
</div>
<?php echo "<div id=\"saveButton\"></div>"; ?>
<div class="clear"></div>