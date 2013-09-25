<?php /* Smarty version 2.6.18, created on 2011-08-24 01:31:49
         compiled from trends_report.stpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'trends_report.stpl', 5, false),array('function', 'ratio', 'trends_report.stpl', 16, false),array('function', 'ratioPercentage', 'trends_report.stpl', 41, false),array('function', 'math', 'trends_report.stpl', 66, false),array('modifier', 'number_span', 'trends_report.stpl', 11, false),array('modifier', 'currency_span', 'trends_report.stpl', 16, false),)), $this); ?>
<!-- trends_report -->
<table class="TrendStats">
    <tbody>
        <tr class="gray">
            <td><?php echo smarty_function_localize(array('str' => 'Impressions'), $this);?>
</td>
            <td><?php echo smarty_function_localize(array('str' => 'raw'), $this);?>
</td>
            <td><?php echo smarty_function_localize(array('str' => 'unique'), $this);?>
</td>
        </tr>
        <tr class="light">
            <td><?php echo smarty_function_localize(array('str' => 'number'), $this);?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['selected']['impressions']->count->raw)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['selected']['impressions']->count->unique)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
        </tr>
        <tr class="dark">
            <td><?php echo smarty_function_localize(array('str' => 'cost'), $this);?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['selected']['transactions']->commission->all,'p2' => $this->_tpl_vars['selected']['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['selected']['transactions']->commission->all,'p2' => $this->_tpl_vars['selected']['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</td>
        </tr>
    </tbody>
</table>        

<table class="TrendStats">
    <tbody>
        <tr class="gray">
            <td><?php echo smarty_function_localize(array('str' => 'Clicks'), $this);?>
</td>
            <td><?php echo smarty_function_localize(array('str' => 'raw'), $this);?>
</td>
            <td><?php echo smarty_function_localize(array('str' => 'unique'), $this);?>
</td>
        </tr>
        <tr class="light">
            <td><?php echo smarty_function_localize(array('str' => 'numbers'), $this);?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['selected']['clicks']->count->raw)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['selected']['clicks']->count->unique)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
        </tr>
        <tr class="dark">
            <td><?php echo smarty_function_localize(array('str' => 'cost'), $this);?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['selected']['transactions']->commission->all,'p2' => $this->_tpl_vars['selected']['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</td>
            <td><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['selected']['transactions']->commission->all,'p2' => $this->_tpl_vars['selected']['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</td>
        </tr>
        <tr class="light">
            <td><?php echo smarty_function_localize(array('str' => 'CTR'), $this);?>
</td>
            <td><span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['selected']['clicks']->count->raw,'p2' => $this->_tpl_vars['selected']['impressions']->count->raw), $this);?>
</span></td>
            <td><span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['selected']['clicks']->count->unique,'p2' => $this->_tpl_vars['selected']['impressions']->count->unique), $this);?>
</span></td>
        </tr>
    </tbody>
</table>       
    
<table class="TrendStats">
    <tbody> 
        <tr class="gray">
            <td><?php echo smarty_function_localize(array('str' => 'Totals'), $this);?>
</td>
            <td></td>
        </tr>    
        <tr class="light">
            <td><?php echo smarty_function_localize(array('str' => 'Commissions'), $this);?>
</td>
            <td><?php echo ((is_array($_tmp=$this->_tpl_vars['selected']['transactions']->commission->all)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>
</td>
        </tr>
        <tr class="dark">
            <?php if ($this->_tpl_vars['selected']['transactionTypesFirstTier']->types[2]->totalCost->all + $this->_tpl_vars['selected']['transactionTypesFirstTier']->types[3]->totalCost->all == 0): ?>
                <td><?php echo smarty_function_localize(array('str' => 'Revenue'), $this);?>
</td>
                <td><?php echo ((is_array($_tmp=$this->_tpl_vars['selected']['transactionsFirstTier']->totalCost->all)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>
</td>
            <?php else: ?>
                <td><?php echo smarty_function_localize(array('str' => 'Revenue'), $this);?>
<br><span style="font-size:10px">total / <span style="color:#B33024">refunds and charge backs</span></span></td>
                <td><?php echo ((is_array($_tmp=$this->_tpl_vars['selected']['transactionsFirstTier']->totalCost->all)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>
 <br>
                    <span style="font-size:10px">
                        <?php echo smarty_function_math(array('equation' => "x+y+z",'x' => $this->_tpl_vars['selected']['transactionsFirstTier']->totalCost->all,'y' => $this->_tpl_vars['selected']['transactionTypesFirstTier']->types[2]->totalCost->all,'z' => $this->_tpl_vars['selected']['transactionTypesFirstTier']->types[3]->totalCost->all,'assign' => 'selectedTotalCost'), $this);?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['selectedTotalCost'])) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>
 / 
                        <?php echo smarty_function_math(array('equation' => "-x-y",'x' => $this->_tpl_vars['selected']['transactionTypesFirstTier']->types[2]->totalCost->all,'y' => $this->_tpl_vars['selected']['transactionTypesFirstTier']->types[3]->totalCost->all,'assign' => 'selectedTotalLoss'), $this);?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['selectedTotalLoss'])) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                    </span>
                </td>
            <?php endif; ?>
        </tr>
        <tr class="light">
            <td><?php echo smarty_function_localize(array('str' => 'avg commission'), $this);?>
</td>
            <td><span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['selected']['transactions']->commission->all,'p2' => $this->_tpl_vars['selected']['transactionsFirstTier']->totalCost->all), $this);?>
</span></td>
        </tr>                        
    </tbody>
</table>