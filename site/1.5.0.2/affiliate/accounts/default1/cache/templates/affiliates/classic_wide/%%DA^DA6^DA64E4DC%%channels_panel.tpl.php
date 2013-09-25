<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:53
         compiled from channels_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'channels_panel.tpl', 2, false),)), $this); ?>
<!-- channels_panel -->
<?php echo smarty_function_localize(array('str' => 'When advertising your affiliate links, you can track them using channels.'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'To use it, simply create a new channel for every link placement you want. In banners you can choose to get the link version with channel appended.'), $this);?>

<?php echo smarty_function_localize(array('str' => 'The channel parameter will be transferred also to the tracked commission, so you will know exactly what link led to the commission.'), $this);?>
<br/><br/>
<?php echo smarty_function_localize(array('str' => 'Examples of use'), $this);?>
:<br/> 					
1. <?php echo smarty_function_localize(array('str' => 'Testing the best placement for the banner on yur website - you can use the same banner, but with different channel for every position - and you will know which one is clicked more or has better CTR.'), $this);?>
<br/><br/>
2. <?php echo smarty_function_localize(array('str' => 'PPC campaigns - you can have multiple ads for the same affiliate link. By using channel unique for every ad you can find out which ad converts more.'), $this);?>

<br/><br/> 					
<?php echo "<div id=\"channelsGrid\"></div>"; ?>