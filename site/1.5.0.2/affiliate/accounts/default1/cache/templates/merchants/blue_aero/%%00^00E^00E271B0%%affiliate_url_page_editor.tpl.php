<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from affiliate_url_page_editor.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_url_page_editor.tpl', 3, false),)), $this); ?>
<!-- affiliate_url_page_editor -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Url page settings'), $this);?>
</legend>

    <table>
        <tr>
            <td>
                <?php echo "<div id=\"FormPanel\" class=\"ScreenSettingsUrlPage\"></div>"; ?>
            </td>
        </tr>
        <tr>
            <td>
                <div class="ScreenSettingsUrlPageSaveField">
                    <?php echo "<div id=\"FormMessage\"></div>"; ?>
                    <?php echo "<div id=\"SaveButton\"></div>"; ?>
                </div>
            </td>
        </tr>

</fieldset>