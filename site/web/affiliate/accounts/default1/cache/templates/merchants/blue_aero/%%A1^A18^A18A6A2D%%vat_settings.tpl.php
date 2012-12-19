<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:53
         compiled from vat_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'vat_settings.tpl', 5, false),)), $this); ?>
<!-- vat_settings -->

<div class="VatForm">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'VAT settings'), $this);?>
</legend>
<?php echo "<div id=\"support_vat\" class=\"VatSettingsForm\"></div>"; ?>
<?php echo "<div id=\"vat_percentage\"></div>"; ?>
<?php echo "<div id=\"vat_computation\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Payout invoice - VAT version'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'HTML format of the invoice for users with VAT applicable.'), $this);?>

<?php echo smarty_function_localize(array('str' => 'You can use Smarty syntax in this template and the constants from the list below.'), $this);?>


<?php echo "<div id=\"payout_invoice_with_vat\"></div>"; ?>
<div class="FormFieldLabel"><div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Payout preview'), $this);?>
</div></div>
<div class="FormFieldInputContainer">
    <div class="FormFieldInput"><?php echo "<div id=\"userid\"></div>"; ?></div>
    <div class="FormFieldHelp"><?php echo "<div id=\"previewInvoiceHelp\"></div>"; ?></div>
    <div><?php echo "<div id=\"previewInvoiceWithVat\"></div>"; ?></div>
    <?php echo "<div id=\"formPanel\"></div>"; ?>
    <div class="FormFieldDescription">
        <?php echo smarty_function_localize(array('str' => 'By clicking Preview invoice you can see how the invoice will look like for the specified affiliate.'), $this);?>

        <br/>
        <?php echo smarty_function_localize(array('str' => 'WARNING: VAT constants will be empty if you show preview for the affiliate that has VAT invoicing disabled.'), $this);?>

    </div>
</div>
<div class="clear"/></div>  
</fieldset>
</div>

<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SaveButton\"></div>"; ?>
<div class="clear"></div>