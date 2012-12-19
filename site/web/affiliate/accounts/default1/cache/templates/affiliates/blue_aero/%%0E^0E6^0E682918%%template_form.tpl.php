<?php /* Smarty version 2.6.18, created on 2012-01-17 14:03:42
         compiled from template_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'template_form.tpl', 3, false),)), $this); ?>
<!-- template_form -->
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Template'), $this);?>
</legend>
    <table>
        <tr><td>
            <table width="100%">
                <tr><td>
                    <div class="FormField">
                            <div class="FormFieldLabel"><div class="Label Inliner Label-mandatory"><?php echo smarty_function_localize(array('str' => 'Theme'), $this);?>
</div></div>
                            <div class="FormFieldInputContainer"><?php echo "<div id=\"theme\"></div>"; ?></div>
                            <div class="clear"></div>
                    </div>
                </td></tr>
                <tr><td>
                    <?php echo "<div id=\"templatename\"></div>"; ?></td></tr>
                <tr><td valign="top">
                    <div class="EditGettingStartedContent">
                	    <?php echo "<div id=\"templatecontent\"></div>"; ?>
                    </div>
                </td></tr>
            </table>
        </td></tr>
        <tr><td>
            <div class="ScreenSettingsSave">
                <?php echo "<div id=\"FormMessage\"></div>"; ?>
            </div>
        </td></tr>
        <tr><td>
            <div class="ScreenSettingsSave">
                <table class="TemplateFormNavigation">
                    <tbody>
                        <tr><td><?php echo "<div id=\"SaveButton\"></div>"; ?></td>
                            <td><?php echo "<div id=\"CancelButton\"></div>"; ?></td></tr>
                    </tbody>
                </table>
            </div>
        </td></tr>
    </table>
</fieldset>