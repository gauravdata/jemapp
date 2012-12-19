<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from integration_methods.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'integration_methods.tpl', 3, false),)), $this); ?>
<!-- integration_methods -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Integration method'), $this);?>
</legend>
    <?php echo smarty_function_localize(array('str' => 'To setup tracking of sales choose one of the integration methods below.'), $this);?>
<br/><br/>
    <?php echo "<div id=\"IntegrationMethods\" class=\"IntegrationMethods\"></div>"; ?>
    <div><?php echo "<div id=\"UseHttps\"></div>"; ?> <?php echo smarty_function_localize(array('str' => 'Use secure connection'), $this);?>
</div>
    <div class="ClearLeft"></div>
    <?php echo "<div id=\"AdvancedOptionsButton\" class=\"FloatLeft\"></div>"; ?>
    <?php echo "<div id=\"AdvancedOptionsPanel\" class=\"ClearLeft\"></div>"; ?>
</fieldset>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Integration steps'), $this);?>
</legend>
    <?php echo "<div id=\"IntegrationMethodBody\"></div>"; ?>
</fieldset>