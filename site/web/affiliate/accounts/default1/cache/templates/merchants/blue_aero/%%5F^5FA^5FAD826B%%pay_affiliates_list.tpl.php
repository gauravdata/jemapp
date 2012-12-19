<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:21
         compiled from pay_affiliates_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'pay_affiliates_list.tpl', 3, false),)), $this); ?>
<table border="0" width="300">
    <tr bgcolor="#d1d1d1">
        <th><?php echo smarty_function_localize(array('str' => 'Name'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Email'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'To pay'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Approved'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Pending'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Declined'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Minimum payout'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Payout method'), $this);?>
</th>        
        <th><?php echo smarty_function_localize(array('str' => 'Payout data'), $this);?>
</th>        
    </tr>
    <?php $_from = $this->_tpl_vars['payaffiliates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['payaffiliate']):
?>
        <tr>
            <td><?php echo $this->_tpl_vars['payaffiliate']->get('firstname'); ?>
 <?php echo $this->_tpl_vars['payaffiliate']->get('lastname'); ?>
</td> 
            <td><?php echo $this->_tpl_vars['payaffiliate']->get('username'); ?>
</td>
            <td><?php echo $this->_tpl_vars['currency']; ?>
<?php echo $this->_tpl_vars['payaffiliate']->get('amounttopay'); ?>
</td>
            <td><?php echo $this->_tpl_vars['currency']; ?>
<?php echo $this->_tpl_vars['payaffiliate']->get('commission'); ?>
</td>
            <td><?php echo $this->_tpl_vars['currency']; ?>
<?php echo $this->_tpl_vars['payaffiliate']->get('pendingAmount'); ?>
</td>
            <td><?php echo $this->_tpl_vars['currency']; ?>
<?php echo $this->_tpl_vars['payaffiliate']->get('declinedAmount'); ?>
</td>
            <td><?php echo $this->_tpl_vars['payaffiliate']->get('minimumpayout'); ?>
</td>
            <td><?php echo $this->_tpl_vars['payaffiliate']->get('payoutMethod'); ?>
</td>
            <td><?php echo $this->_tpl_vars['payaffiliate']->get('payoutData'); ?>
</td>                             
        </tr>
    <?php endforeach; endif; unset($_from); ?>
</table>