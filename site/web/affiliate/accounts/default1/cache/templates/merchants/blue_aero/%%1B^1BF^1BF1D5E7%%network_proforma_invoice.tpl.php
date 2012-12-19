<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:10
         compiled from network_proforma_invoice.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'network_proforma_invoice.tpl', 5, false),)), $this); ?>
<!--	network_proforma_invoice	-->

<div class="NetworkInvoiceForm">
    <fieldset>
        <legend><?php echo smarty_function_localize(array('str' => 'Proforma invoice format'), $this);?>
</legend>
        <?php echo smarty_function_localize(array('str' => 'HTML format of the proforma invoice. You can use Smarty syntax in this template and the constants from the list below.'), $this);?>
<br/>
        <div class="FormFieldLabel"><div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Proforma invoice'), $this);?>
</div></div>
        <div class="FormFieldInputContainer">
            <div class="FormFieldInput"><?php echo "<div id=\"network_proforma_invoiceInput\"></div>"; ?></div>
        </div>
        <div class="clear"></div>
        <div class="FormFieldLabel"><div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Proforma invoice preview'), $this);?>
</div></div>
        <div class="FormFieldInputContainer">
            <div class="FormFieldInput"><?php echo "<div id=\"account\"></div>"; ?></div>
            <div><?php echo "<div id=\"previewButton\"></div>"; ?></div>
            <?php echo "<div id=\"previewPanel\"></div>"; ?>
            <div class="FormFieldDescription"><?php echo smarty_function_localize(array('str' => 'By clicking Preview you can see how the proforma invoice will look like for the specified account.'), $this);?>
</div>
        </div>
        <div class="clear"></div>
    </fieldset>
    <?php echo "<div id=\"saveButton\"></div>"; ?>
</div>