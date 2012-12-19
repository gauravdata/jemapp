<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from application_details_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'application_details_panel.tpl', 6, false),)), $this); ?>
<!-- application_details_panel -->
<div class="ApplicationDetailsPanel">
    <div class="ApplicationMainInfo">
        <h2><?php echo $this->_tpl_vars['postAffiliatePro']; ?>
</h2>
        <h4><?php echo "<div id=\"variation\"></div>"; ?></h4>
        <div class="ProductOf"><?php echo smarty_function_localize(array('str' => 'Product of'), $this);?>
 <a href="<?php echo $this->_tpl_vars['qualityUnitBaseLink']; ?>
" target="_blank" class="ProductOfCompany"><?php echo $this->_tpl_vars['qualityUnit']; ?>
</a></div>
    </div>  
    <div class="ApplicationVersionInfo">
        <div class="ApplicationVersionInfoLine"><?php echo smarty_function_localize(array('str' => 'Version'), $this);?>
: <?php echo "<div id=\"version\" class=\"ActualVersion\"></div>"; ?></div>
        <div class="ApplicationVersionInfoLine"><?php echo "<div id=\"changelogLink\"></div>"; ?></div>
        <div class="ApplicationVersionInfoLine"><?php echo "<div id=\"checkForNewVersion\"></div>"; ?></div>
        <div class="ApplicationVersionInfoLine"><?php echo "<div id=\"License\"></div>"; ?></div>
    </div>
    <div class="ClearBoth"></div>
</div>