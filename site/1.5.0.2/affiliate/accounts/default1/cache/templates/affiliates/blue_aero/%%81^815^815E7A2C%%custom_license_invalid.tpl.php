<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from custom_license_invalid.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'custom_license_invalid.tpl', 7, false),)), $this); ?>
<!--	custom_license_invalid		-->

<div id="Container">
	<div id="InvalidLicenseContainer">
		<div id="Content">
			<div class="InvalidLicense">
				<div class="InvalidLicenseIcon"><?php echo smarty_function_localize(array('str' => 'Invalid License.'), $this);?>
</div>
				<div class="InvalidLicenseText">
				    <?php echo smarty_function_localize(array('str' => 'License is invalid or expired. Please contact your affiliate manager'), $this);?>
 <?php echo "<div id=\"affManagerContact\" class=\"Bold\"></div>"; ?>.
				</div>
			</div>
		</div>		
	</div>
</div>