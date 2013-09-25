<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from payout_invoice_format.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'payout_invoice_format.tpl', 5, false),)), $this); ?>
<!-- payout_invoice_format -->

<div class="PayoutsInvoiceSettingsForm">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Invoicing settings'), $this);?>
</legend>
<?php echo "<div id=\"generate_invoices\"></div>"; ?>
<?php echo "<div id=\"invoice_bcc_recipient\"></div>"; ?>
</fieldset>
</div>

<div class="PayoutsInvoiceForm">
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Payout invoice'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'HTML format of the invoice.'), $this);?>

<?php echo smarty_function_localize(array('str' => 'You can use Smarty syntax in this template and the constants from the list below.'), $this);?>

<br/>

<?php echo "<div id=\"payoutInvoice\"></div>"; ?>
<div class="FormFieldLabel"><div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Payout preview'), $this);?>
</div></div>
<div class="FormFieldInputContainer">
    <div class="FormFieldInput"><?php echo "<div id=\"userid\"></div>"; ?></div>
    <div class="FormFieldHelp"><?php echo "<div id=\"previewInvoiceHelp\"></div>"; ?></div>
    <div><?php echo "<div id=\"previewInvoice\"></div>"; ?></div>
    <?php echo "<div id=\"formPanel\"></div>"; ?>
    <div class="FormFieldDescription"><?php echo smarty_function_localize(array('str' => 'By clicking Preview invoice you can see how the invoice will look like for the specified affiliate.'), $this);?>
</div>
</div>
<div class="clear"/></div>
</fieldset>


<?php echo "<div id=\"SaveButton\"></div>"; ?>
<div class="clear"></div>
</div>