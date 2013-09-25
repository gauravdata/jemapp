<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from admin_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'admin_panel.tpl', 8, false),)), $this); ?>
<!--    admin_panel     -->
<div class="AdminPanel">
	<div class="AdminLinks">
		<?php echo "<div id=\"loginToMerchantPanel\"></div>"; ?>		
	</div>
	<div class="ClearBoth"></div>
<fieldset>       
    <legend><?php echo smarty_function_localize(array('str' => 'Merchant'), $this);?>
</legend>
    <?php echo "<div id=\"firstname\"></div>"; ?>
    <?php echo "<div id=\"lastname\"></div>"; ?>
    <?php echo "<div id=\"username\"></div>"; ?>
    <?php echo "<div id=\"rpassword\"></div>"; ?>
    <?php echo "<div id=\"retypepassword\"></div>"; ?>
    <?php echo "<div id=\"roleid\"></div>"; ?>
    <?php echo "<div id=\"accountid\"></div>"; ?>
    <?php echo "<div id=\"photo\" class=\"AdminPhoto\"></div>"; ?>
</fieldset>

<?php echo "<div id=\"sendButton\"></div>"; ?>
<?php echo "<div id=\"FormMessage\"></div>"; ?>
</div>