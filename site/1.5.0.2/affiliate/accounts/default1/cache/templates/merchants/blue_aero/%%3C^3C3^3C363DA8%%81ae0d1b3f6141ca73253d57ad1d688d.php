<?php /* Smarty version 2.6.18, created on 2011-09-13 12:05:33
         compiled from text://81ae0d1b3f6141ca73253d57ad1d688d */ ?>
<p style="font-family: Arial;">
    <font size="2">
        Hallo <?php echo $this->_tpl_vars['firstname']; ?>
 <?php echo $this->_tpl_vars['lastname']; ?>
,<br><br>wij hebben ontvangen een nieuw wachtwoord verzoek voor uw account <?php echo $this->_tpl_vars['username']; ?>
.
    </font>
</p>
<p style="font-family: Arial;">
    <font size="2">
        Als dit verzoek van u afkomstig is, klik hier <span style="font-weight: bold;"><?php echo $this->_tpl_vars['newPasswordLink']; ?>
</span> of kopiëren URL <span style="font-weight: bold;">http://<?php echo $this->_tpl_vars['newPasswordUrl']; ?>
</span> naar uw browser om uw wachtwoord te wijzigen.<a style="text-decoration: underline; color: rgb(0, 0, 255); font-weight: bold;" href="http://<?php echo $this->_tpl_vars['newPasswordUrl']; ?>
"></a><br>
    </font>
</p>
<p style="font-family: Arial;">
    <font size="2">
        Dit verzoek is geldig tot <span style="font-weight: bold; color: rgb(221, 34, 71);"><?php echo $this->_tpl_vars['validUntil']; ?>
</span>
    </font>
</p>
<font size="2">
    <span style="font-family: Arial;">Life is good.</span><br/><br/>
    <span style="font-family: Arial;">Quality Unit Team</span>
</font>