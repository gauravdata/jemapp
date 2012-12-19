<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from transactions_import.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'transactions_import.tpl', 3, false),)), $this); ?>
<!-- affiliate_fields -->
<div class="TabDescription">
<h3><?php echo smarty_function_localize(array('str' => 'Available fields for affiliate data'), $this);?>
</h3>
<?php echo smarty_function_localize(array('str' => 'Choose which fields you want to use to store data for your affiliates. Some fields are mandatory, and you have up to 25 optional fields for which you can decide what information they will keep, if they will be optional, mandatory, or not displayed at all. These fields will be displayed in affiliate signup form and affiliate profile editation.'), $this);?>

</div>
<div class="TransactionImport">
    <table>
        <tr>
            <td valign='top'>
    			<div class="TransactionImportFields FloatLeft">
        			<fieldset>
            			<legend><?php echo smarty_function_localize(array('str' => 'Import file format'), $this);?>
</legend>
            			<?php echo "<div id=\"fields\"></div>"; ?>
                        <?php echo "<div id=\"addButton\"></div>"; ?>
        			</fieldset>
    			</div>
            </td>
            <td valign='top' >
                <div class="TransactionImportFile FloatLeft">
                    <fieldset>
                        <legend><?php echo smarty_function_localize(array('str' => 'Import file'), $this);?>
</legend>
                        <?php echo "<div id=\"delimiter\"></div>"; ?>
                        <?php echo "<div id=\"source\" class=\"ImportRadioGroup\"></div>"; ?>
                        <?php echo "<div id=\"url\"></div>"; ?>
                        <?php echo "<div id=\"uploadFile\"></div>"; ?>
                        <?php echo "<div id=\"exportFilesGrid\"></div>"; ?> 
                        <?php echo "<div id=\"serverFile\"></div>"; ?>
                        <?php echo "<div id=\"skipFirstRow\"></div>"; ?>
                        <?php echo "<div id=\"transactionType\"></div>"; ?>
                        <?php echo "<div id=\"importButton\"></div>"; ?>
                    </fieldset>
                </div>
                <div class="clear">
                <div class="TransactionImportSettings FloatLeft">
                    <fieldset>
                        <legend><?php echo smarty_function_localize(array('str' => 'Transaction import settings'), $this);?>
</legend>
                        <?php echo "<div id=\"computeAtomaticaly\"></div>"; ?>
                        <?php echo "<div id=\"matchTransaction\"></div>"; ?> 
                        <?php echo "<div id=\"transactionStatus\"></div>"; ?>
                    </fieldset>
                </div>
            </td>
        </tr>
    </table>
</div>