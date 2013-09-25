<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from set_new_password_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'set_new_password_form.tpl', 6, false),)), $this); ?>
<!-- set_new_password_form -->
<div class="LoginMain">
	<div class="Window-active">
		<div class="WindowHeaderLeft"><div class="WindowHeaderRight"><div class="WindowHeader">
			<div class="LogoutSmallIcon SmallIcon" tabindex="0"></div>
			<div class="WindowHeaderTitle"><?php echo smarty_function_localize(array('str' => 'Set New Password'), $this);?>
</div>  
		</div></div></div>
		<div class="WindowLeft"><div class="WindowRight"><div class="WindowIn"><div class="WindowContent">
			<?php echo "<div id=\"username\"></div>"; ?>
            <?php echo "<div id=\"password\"></div>"; ?>
            <?php echo "<div id=\"set_pw_captcha\"></div>"; ?>
			<?php echo "<div id=\"FormMessage\"></div>"; ?>
			<?php echo "<div id=\"SendButton\"></div>"; ?>
			<?php echo "<div id=\"backToLogin\"></div>"; ?>
			<div class="clear"></div>
		</div></div></div></div>
		<div class="WindowBottomLeft"><div class="WindowBottomRight"><div class="WindowBottom"></div></div></div>
	</div>
</div>