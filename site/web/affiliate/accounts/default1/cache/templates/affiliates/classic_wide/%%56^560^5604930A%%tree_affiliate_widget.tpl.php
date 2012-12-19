<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:46
         compiled from tree_affiliate_widget.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'tree_affiliate_widget.tpl', 6, false),)), $this); ?>
<!-- tree_affiliate_widget -->


<div class="Tree">
<?php echo "<div id=\"refid\" class=\"TreeUserId\"></div>"; ?>
<?php echo "<div id=\"name\" class=\"TreeName\"></div>"; ?>&nbsp;&nbsp; [ <?php echo "<div id=\"subAffCount\" class=\"Inline\"></div>"; ?> <?php echo smarty_function_localize(array('str' => 'subaffiliate(s)'), $this);?>
 ]
<?php echo "<div id=\"username\" class=\"TreeUserName\"></div>"; ?>
</div>


