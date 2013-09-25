<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from affiliate_screen_header_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_screen_header_edit.tpl', 7, false),)), $this); ?>
<!-- affiliate_screen_header_edit -->    
<?php echo "<div id=\"showheader\" class=\"ScreenSettingsShow\"></div>"; ?>
<table>
    <tr>
        <td rowspan="2" valign="top">
            <div class="FormFieldLabel">
                <div class="Label Inliner Label-mandatory"><?php echo smarty_function_localize(array('str' => 'Icon'), $this);?>
</div>
            </div>
            <div class="clear"></div>
            <?php echo "<div id=\"iconInput\"></div>"; ?>
        </td>
        <td><?php echo "<div id=\"title\" class=\"ScreenSettingsTitle\"></div>"; ?></td></tr>
    <tr><td><?php echo "<div id=\"description\" class=\"ScreenSettingsDescription\"></div>"; ?></td></tr>
</table>