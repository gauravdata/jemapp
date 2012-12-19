<?php /* Smarty version 2.6.18, created on 2011-12-16 03:38:51
         compiled from login_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'login_form.tpl', 8, false),)), $this); ?>
<!-- login_form -->
<div class="LoginMain">
<a class="Logo" title="<?php echo $this->_tpl_vars['programName']; ?>
" href="index.php"/>
	           <img src="<?php echo $this->_tpl_vars['programLogo']; ?>
" class="LogoImage"></a>
	<div class="Window-active">
		<div class="WindowHeaderLeft"><div class="WindowHeaderRight"><div class="WindowHeader">
			<div class="LogoutSmallIcon SmallIcon" tabindex="0"></div>
			<div class="WindowHeaderTitle"><?php echo smarty_function_localize(array('str' => 'Login'), $this);?>
</div>  
		</div></div></div>
		<div class="WindowLeft"><div class="WindowRight"><div class="WindowIn"><div class="WindowContent">
			<?php echo "<div id=\"username\"></div>"; ?>
			<?php echo "<div id=\"password\"></div>"; ?>
            <?php echo "<div id=\"language\" class=\"LanguageSelector\"></div>"; ?>
			<div class="CheckBoxContainer"><?php echo "<div id=\"rememberMeInput\"></div>"; ?> <?php echo "<div id=\"rememberMeLabel\"></div>"; ?></div>
			<div class="clear"></div>
			<?php echo "<div id=\"FormMessage\"></div>"; ?>
			<div class="clear"></div>
			<?php echo "<div id=\"LoginButton\"></div>"; ?>
			<?php echo "<div id=\"ForgottenPasswordLink\"></div>"; ?>
			<div class="LoginIn"><?php echo $this->_tpl_vars['papCopyrightText']; ?>
</div>
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