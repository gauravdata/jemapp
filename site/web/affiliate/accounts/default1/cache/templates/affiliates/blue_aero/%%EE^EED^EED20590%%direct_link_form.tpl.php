<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from direct_link_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'direct_link_form.tpl', 2, false),)), $this); ?>
<!-- direct_link_form -->
<?php echo smarty_function_localize(array('str' => '<h3>Add / edit DirectLink URL</h3>'), $this);?>


<fieldset>
<?php echo "<div id=\"url\"></div>"; ?>

<?php echo "<div id=\"note\"></div>"; ?>
</fieldset>
<fieldset>
  <legend>Additional tracking</legend>
  <div class="HintText"><?php echo smarty_function_localize(array('str' => 'You can set that the click from this URL will belong to a selected channel, 
  banner or campaign. If you don\'t select anything, the default campaign will be used.'), $this);?>

<?php echo "<div id=\"channelid\"></div>"; ?>

<div class="Line"></div>
<?php echo "<div id=\"campaignid\"></div>"; ?>
<?php echo smarty_function_localize(array('str' => 'or'), $this);?>

<?php echo "<div id=\"bannerid\"></div>"; ?>
</fieldset>
<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>