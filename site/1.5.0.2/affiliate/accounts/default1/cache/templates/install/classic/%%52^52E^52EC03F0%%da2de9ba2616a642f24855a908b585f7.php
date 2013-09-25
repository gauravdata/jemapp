<?php /* Smarty version 2.6.18, created on 2011-08-17 13:57:35
         compiled from text://da2de9ba2616a642f24855a908b585f7 */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'currency', 'text://da2de9ba2616a642f24855a908b585f7', 4, false),)), $this); ?>
<font size="2">
<span style="font-family: Arial;">Geachte <?php echo $this->_tpl_vars['firstname']; ?>
 <?php echo $this->_tpl_vars['lastname']; ?>
,</span><br/><br/>
<span style="font-family: Arial;">new sale / lead was registered by our affiliate program with status: <?php echo $this->_tpl_vars['status']; ?>
.</span><br/><br/><font size="4"><strong style="font-family: Arial;"><?php if ($this->_tpl_vars['rawtype'] == 'U'): ?>Recurring sale<?php else: ?>Verkoop<?php endif; ?> details:</strong></font><br/>
<span style="font-family: Arial;">Totale kosten: </span><strong style="font-family: Arial;"><?php echo ((is_array($_tmp=$this->_tpl_vars['totalcost'])) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</strong><br/>
<span style="font-family: Arial;">Provisie uit deze verkoop: </span><strong style="font-family: Arial;"><?php echo ((is_array($_tmp=$this->_tpl_vars['commission'])) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</strong><br/>
<span style="font-family: Arial;">Order-id: </span><strong style="font-family: Arial;"><?php echo $this->_tpl_vars['orderid']; ?>
</strong><br/>
<span style="font-family: Arial;">Produkt ID: </span><strong style="font-family: Arial;"><?php echo $this->_tpl_vars['productid']; ?>
</strong><br/>
<span style="font-family: Arial;">IP-adres: </span><strong style="font-family: Arial;"><?php echo $this->_tpl_vars['ip']; ?>
</strong><br/>
<span style="font-family: Arial;">Referentie Url: </span><strong style="font-family: Arial;"><?php echo $this->_tpl_vars['refererurl']; ?>
</strong><br/><br/>
<span style="font-family: Arial;">Met vriendelijke groet,,</span><br/><br/>
<span style="font-family: Arial;">Uw Partner Manager</span>
</font>