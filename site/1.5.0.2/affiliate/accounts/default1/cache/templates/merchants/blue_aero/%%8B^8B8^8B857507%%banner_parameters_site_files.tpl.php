<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:17
         compiled from banner_parameters_site_files.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'banner_parameters_site_files.tpl', 3, false),)), $this); ?>
<!-- banner_parameters_site_files -->
<fieldset class="BannerSite">
<legend><?php echo smarty_function_localize(array('str' => 'Replicated site files'), $this);?>
</legend>
    <table width="100%">
        <tr><td width="60%" valign="top">
                <?php echo "<div id=\"data3\" class=\"SourceType\"></div>"; ?>            
            </td>
            <td width="40%" valign="top">
                <?php echo "<div id=\"variableList\" class=\"ListOfVariables\"></div>"; ?>
            </td>
        </tr>
    </table>
</fieldset>