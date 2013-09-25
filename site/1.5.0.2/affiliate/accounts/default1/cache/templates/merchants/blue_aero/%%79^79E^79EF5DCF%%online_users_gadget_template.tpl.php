<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:45
         compiled from online_users_gadget_template.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'online_users_gadget_template.tpl', 9, false),)), $this); ?>
<!-- online_users_gadget_template -->

<div class="GadgetInTopLeft"><div class="GadgetInTopRight"><div class="GadgetInTop">
</div></div></div>

<div class="GadgetInLeft"><div class="GadgetInRight">

<div class="GadgetInMain">        
<span class="orange"><?php echo smarty_function_localize(array('str' => 'Currently online:'), $this);?>
</span>
<?php echo "<div id=\"onlineUsersData\"></div>"; ?>
<?php echo "<div id=\"onlineUsersListLink\"></div>"; ?><br/>
<?php echo "<div id=\"loginsHistoryLink\"></div>"; ?>
</div>

</div></div>

<div class="GadgetInBottomLeft"><div class="GadgetInBottomRight"><div class="GadgetInBottom">
</div></div></div>
        