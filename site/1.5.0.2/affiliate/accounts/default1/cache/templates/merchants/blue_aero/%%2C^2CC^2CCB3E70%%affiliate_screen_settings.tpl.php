<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from affiliate_screen_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_screen_settings.tpl', 3, false),)), $this); ?>
<!-- affiliate_screen_settings -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Header'), $this);?>
</legend>
    <table>
        <tr><td>
            <?php echo "<div id=\"HeaderEdit\"></div>"; ?> 
            <div class="clear"></div>
        </td></tr>
        <tr><td>
            <div class="ScreenSettingsSave">
                <?php echo "<div id=\"FormMessage\"></div>"; ?>
            </div>
        </td></tr>
        <tr><td>
            <div class="ScreenSettingsSave">
                <?php echo "<div id=\"SaveButton\"></div>"; ?>
            </div>
        </td></tr>
    </table>
    <div class="clear"></div>
</fieldset>

<?php echo "<div id=\"EditContent\"></div>"; ?>