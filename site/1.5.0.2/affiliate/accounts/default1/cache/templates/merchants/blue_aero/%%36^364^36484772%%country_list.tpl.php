<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:38
         compiled from country_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'country_list.tpl', 3, false),)), $this); ?>
<!--    country_list    -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Countries'), $this);?>
</legend>
    <?php echo "<div id=\"grid\"></div>"; ?>
</fieldset>