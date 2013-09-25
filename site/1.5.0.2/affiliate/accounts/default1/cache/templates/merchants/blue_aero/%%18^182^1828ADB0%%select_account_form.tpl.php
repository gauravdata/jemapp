<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from select_account_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'select_account_form.tpl', 9, false),)), $this); ?>
<!--	select_account_form	-->

<!-- request_new_password_form -->
<div class="LoginMain">
<a href="#" id="Logo" title="Logo"></a>
	<div class="Window-active">
		<div class="WindowHeaderLeft"><div class="WindowHeaderRight"><div class="WindowHeader">
			<div class="LogoutSmallIcon SmallIcon" tabindex="0"></div>
			<div class="WindowHeaderTitle"><?php echo smarty_function_localize(array('str' => 'Select an account'), $this);?>
</div>  
		</div></div></div>
		<div class="WindowLeft"><div class="WindowRight"><div class="WindowIn"><div class="WindowContent">
		    <?php echo smarty_function_localize(array('str' => 'Please select an account:'), $this);?>

			<?php echo "<div id=\"accountid\" class=\"SelectAccount\"></div>"; ?>
			<?php echo "<div id=\"formMessage\"></div>"; ?>
			<div class="clear"></div>
			<?php echo "<div id=\"sendButton\"></div>"; ?>
			<div class="clear"></div>
		</div></div></div></div>
		<div class="WindowBottomLeft"><div class="WindowBottomRight"><div class="WindowBottom"></div></div></div>
		
		
<div class="SupportBrowsers">
<div class="SupportStrap"><?php echo smarty_function_localize(array('str' => 'Best viewed in:'), $this);?>
</div>
<a class="Firefox" title="Firefox 3"></a>
<a class="Ie" title="Internet Explorer 7"></a>
<a class="Safari" title="Safari"></a>
<a class="Opera" title="Opera 9.5"></a>
<a class="Chrome" title="Google Chrome"></a>	
</div>

	</div>
</div>