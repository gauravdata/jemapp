<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:38
         compiled from cookies_tab.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'cookies_tab.tpl', 5, false),)), $this); ?>
<!-- cookies_tab -->

<div class="CookiesForm">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Cookies privacy policy'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Cookie privacy policy influences if the tracking cookies will be blocked by browsers, so it si important to set it.<br/>You should set at least Compact P3P policy. If you don\'t want to generate it for your site, use the following string: NOI NID ADMa DEVa PSAa OUR BUS ONL UNI COM STA OTC'), $this);?>

<?php echo "<div id=\"url_to_p3p\"></div>"; ?>
<?php echo "<div id=\"p3p_policy_compact\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Tracking related settings'), $this);?>
</legend>
<?php echo "<div id=\"cookie_domain\" class=\"CookieDomain\"></div>"; ?>
<div class="Line"></div>
<?php echo "<div id=\"overwrite_cookie\" class=\"OverwriteCookie\"></div>"; ?>
<div class="Line"></div>
<?php echo "<div id=\"delete_cookie\" class=\"OverwriteCookie\"></div>"; ?>
</fieldset>
</div>

<?php echo "<div id=\"SaveButton\"></div>"; ?>
<div class="clear"></div>