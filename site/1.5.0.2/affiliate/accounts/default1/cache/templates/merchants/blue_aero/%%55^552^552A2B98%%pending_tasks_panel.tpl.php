<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from pending_tasks_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'pending_tasks_panel.tpl', 4, false),)), $this); ?>
<!--	pending_tasks_panel		-->

<br/>
<div class="StatsSectionTitle"><?php echo smarty_function_localize(array('str' => 'Tasks waiting for approval'), $this);?>
</div>
<?php echo "<div id=\"pendingTasks\"></div>"; ?>
<br/>