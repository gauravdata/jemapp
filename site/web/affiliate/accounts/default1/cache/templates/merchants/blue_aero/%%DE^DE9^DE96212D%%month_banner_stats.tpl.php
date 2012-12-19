<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:10
         compiled from month_banner_stats.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'month_banner_stats.tpl', 2, false),)), $this); ?>
<!-- month_banner_stats -->
<?php echo smarty_function_localize(array('str' => 'You have'), $this);?>
&nbsp;<?php echo "<div id=\"impressions\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'impressions,'), $this);?>
&nbsp;<?php echo "<div id=\"rawClicks\"></div>"; ?>/<?php echo "<div id=\"uniqueClicks\"></div>"; ?>
&nbsp;<?php echo smarty_function_localize(array('str' => 'clicks (raw/unique) and'), $this);?>
&nbsp;<?php echo "<div id=\"sales\"></div>"; ?>&nbsp;<?php echo smarty_function_localize(array('str' => 'sales.'), $this);?>

<br/>
<?php echo smarty_function_localize(array('str' => 'Your CTR was'), $this);?>
&nbsp;<?php echo "<div id=\"ctr\"></div>"; ?>%.