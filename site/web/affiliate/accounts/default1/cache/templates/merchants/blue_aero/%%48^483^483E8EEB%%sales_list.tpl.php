<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from sales_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'sales_list.tpl', 3, false),array('modifier', 'currency', 'sales_list.tpl', 19, false),array('modifier', 'date', 'sales_list.tpl', 23, false),)), $this); ?>
<table border="0" width="300">
    <tr bgcolor="#d1d1d1">
        <th><?php echo smarty_function_localize(array('str' => 'ID'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Commission'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Total cost'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Order ID'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Product ID'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Created'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Campaign name'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Type'), $this);?>
</th>        
        <th><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Paid'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Affiliate'), $this);?>
</th>
        <th><?php echo smarty_function_localize(array('str' => 'Channel'), $this);?>
</th>
    </tr>
    <?php $_from = $this->_tpl_vars['sales']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sale']):
?>
        <tr>
            <td><?php echo $this->_tpl_vars['sale']->get('id'); ?>
</td> 
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['sale']->get('commission'))) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['sale']->get('totalcost'))) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</td>
            <td><?php echo $this->_tpl_vars['sale']->get('orderid'); ?>
</td>
            <td><?php echo $this->_tpl_vars['sale']->get('productid'); ?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['sale']->get('dateinserted'))) ? $this->_run_mod_handler('date', true, $_tmp) : smarty_modifier_date($_tmp)); ?>
</td>
            <td><?php echo $this->_tpl_vars['sale']->get('name'); ?>
</td>
            <td><?php echo $this->_tpl_vars['sale']->get('rtype'); ?>
</td>            
            <td><?php echo $this->_tpl_vars['sale']->get('rstatus'); ?>
</td>
            <td><?php echo $this->_tpl_vars['sale']->get('payoutstatus'); ?>
</td>
            <td><?php echo $this->_tpl_vars['sale']->get('firstname'); ?>
 <?php echo $this->_tpl_vars['sale']->get('lastname'); ?>
</td>
            <td><?php echo $this->_tpl_vars['sale']->get('channel'); ?>
</td>                                 
        </tr>
    <?php endforeach; endif; unset($_from); ?>
</table>