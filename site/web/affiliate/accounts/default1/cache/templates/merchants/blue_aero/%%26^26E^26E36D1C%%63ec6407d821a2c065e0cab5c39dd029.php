<?php /* Smarty version 2.6.18, created on 2012-04-05 06:29:40
         compiled from text://63ec6407d821a2c065e0cab5c39dd029 */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'currency', 'text://63ec6407d821a2c065e0cab5c39dd029', 5, false),)), $this); ?>
<font size="2">
<span style="font-family: Arial;">Beste <?php echo $this->_tpl_vars['firstname']; ?>
 <?php echo $this->_tpl_vars['lastname']; ?>
,</span><br><br>
<span style="font-family: Arial;">I would like to let you know that we paid your commissions earned in our No Tomatoes affiliate program.</span><br><br><strong style="font-family: Arial;">.:Uitbetaling tonen:.</strong><br><br>
<span style="font-family: Arial;">Datum: </span><strong style="font-family: Arial;"><?php echo $this->_tpl_vars['date']; ?>
</strong><br>
<span style="font-family: Arial;">Bedrag betaald: </span> <strong style="font-family: Arial;"><?php echo ((is_array($_tmp=$this->_tpl_vars['payment'])) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</strong><br>
<span style="font-family: Arial;">Uitbetaling methode: </span><strong style="font-family: Arial;"><?php echo $this->_tpl_vars['payoutmethod']; ?>
</strong><br><br>
<br><span style="font-family: Arial;">Life is good.</span><br><br>
<span style="font-family: Arial;">No Tomatoes<br><br><span style="font-weight: bold;">Life is good.</span><br style="font-weight: bold;"><span style="font-weight: bold;">No Tomatoes</span><br></span>
</font>