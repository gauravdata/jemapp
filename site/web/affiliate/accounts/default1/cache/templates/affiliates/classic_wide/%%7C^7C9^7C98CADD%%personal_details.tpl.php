<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:35
         compiled from personal_details.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'personal_details.tpl', 6, false),)), $this); ?>
<!-- personal_details -->
<div class="AffiliateForm">
    <table>
    <tr><td valign="top">
            <fieldset>
                <legend><?php echo smarty_function_localize(array('str' => 'Personal Info'), $this);?>
</legend>
                <?php echo "<div id=\"username\"></div>"; ?>
                <?php echo "<div id=\"rpassword\"></div>"; ?>
                <?php echo "<div id=\"firstname\"></div>"; ?>
                <?php echo "<div id=\"lastname\"></div>"; ?>
                <?php echo "<div id=\"refid\"></div>"; ?>
                <?php echo "<div id=\"photo\" class=\"AffiliateFormPhoto\"></div>"; ?>
            </fieldset>
            <?php echo "<div id=\"DynamicFields1\"></div>"; ?>
        </td>
        <td valign="top" class="AffiliateFormAdditionalInfo">
            <?php echo "<div id=\"DynamicFields2\"></div>"; ?>
        </td></tr>
    </table>
    <?php echo "<div id=\"FormMessage\"></div>"; ?>
    <?php echo "<div id=\"SaveButton\"></div>"; ?>
    <div class="clear"></div>
</div>