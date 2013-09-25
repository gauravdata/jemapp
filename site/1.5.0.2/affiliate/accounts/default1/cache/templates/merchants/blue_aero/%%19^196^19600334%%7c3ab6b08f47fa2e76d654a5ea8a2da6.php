<?php /* Smarty version 2.6.18, created on 2012-04-05 06:29:40
         compiled from text://7c3ab6b08f47fa2e76d654a5ea8a2da6 */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'currency_span', 'text://7c3ab6b08f47fa2e76d654a5ea8a2da6', 12, false),)), $this); ?>
<b>Invoice Number:</b> <?php echo $this->_tpl_vars['invoicenumber']; ?>
<br/>
    <b>Invoice date:</b> <?php echo $this->_tpl_vars['date']; ?>
<br/>
    <br/>
    <b>Affiliate Details:</b><br/>
    <?php echo $this->_tpl_vars['firstname']; ?>
 <?php echo $this->_tpl_vars['lastname']; ?>
 (<?php echo $this->_tpl_vars['username']; ?>
)<br/>
    <?php echo $this->_tpl_vars['data2']; ?>
<br/>
    <?php echo $this->_tpl_vars['data3']; ?>
<br/>
    <?php echo $this->_tpl_vars['data7']; ?>
 <?php echo $this->_tpl_vars['data4']; ?>
<br/>
    <?php echo $this->_tpl_vars['data5']; ?>
 <?php echo $this->_tpl_vars['data6']; ?>
<br/>
    <br/>
    <b>Payment Details:</b> Affiliate commissions<br/>
    Amount: <?php echo ((is_array($_tmp=$this->_tpl_vars['payment'])) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>
<br/>
    VAT (<?php echo $this->_tpl_vars['vat_percentage']; ?>
%): <?php echo ((is_array($_tmp=$this->_tpl_vars['payment_vat_part'])) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>
<br/>
    <br/>
    <b>Note:</b><br/>
    <?php echo $this->_tpl_vars['affiliate_note']; ?>
<br/>