<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:21
         compiled from license_invalid.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'license_invalid.tpl', 7, false),)), $this); ?>
<!-- license_invalid -->
<div id="Container">
	<div id="InvalidLicenseContainer">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<div id="Content">
			<div class="InvalidLicense">
				<div class="InvalidLicenseIcon"><?php echo smarty_function_localize(array('str' => 'Invalid License.'), $this);?>
</div>
				<div class="InvalidLicenseText">
				    Your license is invalid or expired. There may be several reasons for this:<div class="clear"></div><br/>
				    1. You have moved your installation to different url.<div class="clear"></div></br/>
			    	2. Your license id has changed or expired.<div class="clear"></div><br/><br/>
			    	<?php echo "<div id=\"RevalidateLicenseButton\"></div>"; ?><div class="clear"></div><br/><br/></br/>
			    	Set new license id <?php echo "<div id=\"NewLicenseId\"></div>"; ?> <?php echo "<div id=\"UpdateLicenseButton\"></div>"; ?>    <div class="clear"></div><br/><br/></br/>
			    	If you believe this is an error, contact us at <a href="<?php echo $this->_tpl_vars['qualityUnitBaseLink']; ?>
"><?php echo $this->_tpl_vars['qualityUnitBaseLink']; ?>
</a>
				</div>
			</div>
		</div>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>