<?php /* Smarty version 2.6.18, created on 2012-05-09 07:37:38
         compiled from text://cc36927fa1376ce16c18f419a192f1d2 */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'currency', 'text://cc36927fa1376ce16c18f419a192f1d2', 5, false),)), $this); ?>
<font size="2">
<span style="font-family: Arial;">Beste <?php echo $this->_tpl_vars['firstname']; ?>
 <?php echo $this->_tpl_vars['lastname']; ?>
,</span><br/><br/>
<span style="font-family: Arial;">Wij willen u laten weten dat we uw verdiende in ons affiliate programma provisie betaald hebben.</span><br/><br/><strong style="font-family: Arial;">.:Payout preview:.</strong><br/><br/>
<span style="font-family: Arial;">Datum: </span><strong style="font-family: Arial;"><?php echo $this->_tpl_vars['date']; ?>
</strong><br/>
<span style="font-family: Arial;">Bedrag betaald: </span><strong style="font-family: Arial;"><?php echo ((is_array($_tmp=$this->_tpl_vars['payment'])) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</strong><br/>
<span style="font-family: Arial;">Uitbetaling methode: </span><strong style="font-family: Arial;"><?php echo $this->_tpl_vars['payoutmethod']; ?>
</strong><br/><br/>
<span style="font-family: Arial;">Met vriendelijke groet,,</span><br/><br/>
<span style="font-family: Arial;">No Tomatoes</span>
</font>