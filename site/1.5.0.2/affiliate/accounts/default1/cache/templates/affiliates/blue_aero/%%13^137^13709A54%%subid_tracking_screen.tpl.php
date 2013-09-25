<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:42
         compiled from subid_tracking_screen.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'subid_tracking_screen.tpl', 3, false),)), $this); ?>
<!-- subid_tracking_screen -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Link (SubId) Tracking'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'SubId tracking can be made using <strong>Channels</strong>. For additional tracking, you can append up to two custom parameters <b>data1</b> and <b>data2</b> to the link URL.<br/>These parameters will be transferred also to the tracked commission, so you will know exactly what link led to the commission.'), $this);?>
 					
<p/>
<?php echo smarty_function_localize(array('str' => 'Example SubId link'), $this);?>

<?php echo "<div id=\"exampleLink\"></div>"; ?>
<p/>
</fieldset>