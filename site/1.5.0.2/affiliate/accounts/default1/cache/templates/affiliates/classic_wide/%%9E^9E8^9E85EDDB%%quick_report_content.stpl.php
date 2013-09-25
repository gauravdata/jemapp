<?php /* Smarty version 2.6.18, created on 2011-08-17 14:49:19
         compiled from quick_report_content.stpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'quick_report_content.stpl', 5, false),array('function', 'ratioPercentage', 'quick_report_content.stpl', 28, false),array('function', 'ratio', 'quick_report_content.stpl', 123, false),array('modifier', 'number_span', 'quick_report_content.stpl', 16, false),array('modifier', 'currency_span', 'quick_report_content.stpl', 106, false),array('modifier', 'currency', 'quick_report_content.stpl', 107, false),)), $this); ?>
<!-- quick_report_content -->

<div class="OverviewDataBox">
    <div class="OverviewDataBoxContent">
        <div class="OverviewHeader"><strong><?php echo smarty_function_localize(array('str' => 'Counts'), $this);?>
</strong></div>
        <div class="OverviewInnerBox">
            <table class="StatsSummaries">
                <tr class="gray">
                    <td></td>
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Raw'), $this);?>
</td>
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Unique'), $this);?>
</td>
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Declined'), $this);?>
</td>
                </tr>
                <tr>
                    <td align='left'><?php echo smarty_function_localize(array('str' => 'Clicks'), $this);?>
</td>
                    <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['clicks']->count->raw)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                    <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['clicks']->count->unique)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                    <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['clicks']->count->declined)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                </tr>
                <tr>
                    <td align='left'><?php echo smarty_function_localize(array('str' => 'Impressions'), $this);?>
</td>
                    <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['impressions']->count->raw)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                    <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['impressions']->count->unique)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                    <td align="right"><span class="NumberData">-</span></td>
                </tr>
                <tr>
                    <td><?php echo smarty_function_localize(array('str' => 'CTR'), $this);?>
</td>
                    <td align="right"><?php echo ((is_array($_tmp=smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['clicks']->count->raw,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp));?>
</td>
                    <td align="right"><?php echo ((is_array($_tmp=smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['clicks']->count->unique,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp));?>
</td>
                    <td align="right"><span class="NumberData">-</span></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="OverviewDataBox">
    <div class="OverviewDataBoxContent">
        <div class="OverviewHeader"><strong><?php echo smarty_function_localize(array('str' => 'Sale counts'), $this);?>
</strong></div>
        <div class="OverviewInnerBox">
            <table class="StatsSummaries">
                <tr class="gray">
                    <td rowspan="2"></td>
                    <td align="center" rowspan="2"><?php echo smarty_function_localize(array('str' => 'Pending'), $this);?>
</td>
                    <td align="center" rowspan="2"><?php echo smarty_function_localize(array('str' => 'Declined'), $this);?>
</td>
                    <td align="center" colspan="2"><?php echo smarty_function_localize(array('str' => 'Approved'), $this);?>
</td>
                </tr>
                <tr class="gray">
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Unpaid'), $this);?>
</td>
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Paid'), $this);?>
</td>
                </tr>
                <?php echo $this->_tpl_vars['sumTransaction']->clear(); ?>

                <?php $_from = $this->_tpl_vars['transactionTypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['transactions']):
?>
                    <?php if ($this->_tpl_vars['transactions']->type != 'C' && $this->_tpl_vars['transactions']->type != 'R' && $this->_tpl_vars['transactions']->type != 'H'): ?>
                        <?php echo $this->_tpl_vars['sumTransaction']->add($this->_tpl_vars['transactions']); ?>

                        <tr>
                            <td align="left"><?php echo $this->_tpl_vars['transactions']->name; ?>
</td>
                            <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->count->pending)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                            <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->count->declined)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                            <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->count->approved)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                            <td align="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->count->paid)) ? $this->_run_mod_handler('number_span', true, $_tmp) : smarty_modifier_number_span($_tmp)); ?>
</td>
                        </tr>
                     <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>
                <tr>
                    <td align="left"><?php echo smarty_function_localize(array('str' => 'Conversion ratio (Raw/Unique)'), $this);?>
 <?php echo $this->_tpl_vars['sumTransaction']->count->pending; ?>
</td>
                    <td align="right"><span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['clicks']->count->raw), $this);?>
</span>
                                        /<span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['clicks']->count->unique), $this);?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['clicks']->count->raw), $this);?>
</span>
                                        /<span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['clicks']->count->unique), $this);?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['clicks']->count->raw), $this);?>
</span>
                                        /<span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['clicks']->count->unique), $this);?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['clicks']->count->raw), $this);?>
</span>
                                        /<span class="NumberData"><?php echo smarty_function_ratioPercentage(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['clicks']->count->unique), $this);?>
</span></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="OverviewDataBox">
    <div class="OverviewDataBoxContent">
        <div class="OverviewHeader">
            <strong>
                <?php echo smarty_function_localize(array('str' => 'Commissions'), $this);?>

            </strong>
        </div>
        <div class="OverviewInnerBox">
            <table class="StatsSummaries">
                <tr class="gray">
                    <td rowspan="2"></td>
                    <td align="center" rowspan="2"><?php echo smarty_function_localize(array('str' => 'Pending'), $this);?>
</td>
                    <td align="center" rowspan="2"><?php echo smarty_function_localize(array('str' => 'Declined'), $this);?>
</td>
                    <td align="center" colspan="2"><?php echo smarty_function_localize(array('str' => 'Approved'), $this);?>
</td>
                </tr>
                <tr class="gray">
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Unpaid'), $this);?>
</td>
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Paid'), $this);?>
</td>
                </tr>
                <?php echo $this->_tpl_vars['sumTransaction']->clear(); ?>

                <?php $_from = $this->_tpl_vars['transactionTypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['transactions']):
?>
                    <?php if ($this->_tpl_vars['transactions']->type != 'R' && $this->_tpl_vars['transactions']->type != 'H'): ?>
                        <?php echo $this->_tpl_vars['sumTransaction']->add($this->_tpl_vars['transactions']); ?>

                        <tr>
                            <td align="left" valign="top"><?php echo $this->_tpl_vars['transactions']->name; ?>
</td>
                            <td align="right" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->commission->pending)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                              <?php if ($this->_tpl_vars['transactions']->refunds->commission->pending != 0): ?> <br/><span class="NumberDataRed"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->refunds->commission->pending)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?>
                              <?php if ($this->_tpl_vars['transactions']->chargebacks->commission->pending != 0): ?> <br/><span class="NumberDataOrange"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->chargebacks->commission->pending)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?></td>
                            <td align="right" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->commission->declined)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                              <?php if ($this->_tpl_vars['transactions']->refunds->commission->declined != 0): ?> <br/><span class="NumberDataRed"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->refunds->commission->declined)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?>
                              <?php if ($this->_tpl_vars['transactions']->chargebacks->commission->declined != 0): ?> <br/><span class="NumberDataOrange"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->chargebacks->commission->declined)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?></td>
                            <td align="right" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->commission->approved)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                              <?php if ($this->_tpl_vars['transactions']->refunds->commission->approved != 0): ?> <br/><span class="NumberDataRed"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->refunds->commission->approved)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?>
                              <?php if ($this->_tpl_vars['transactions']->chargebacks->commission->approved != 0): ?> <br/><span class="NumberDataOrange"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->chargebacks->commission->approved)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?></td>
                            <td align="right" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->commission->paid)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                              <?php if ($this->_tpl_vars['transactions']->refunds->commission->paid != 0): ?> <br/><span class="NumberDataRed"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->refunds->commission->paid)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?>
                              <?php if ($this->_tpl_vars['transactions']->chargebacks->commission->paid != 0): ?> <br/><span class="NumberDataOrange"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->chargebacks->commission->paid)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?></td>
                        </tr>
                     <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>
                <tr>
                    <td align="left"><?php echo smarty_function_localize(array('str' => 'Avg. commission per click (raw/unique)'), $this);?>
</td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                </tr>
                <tr>
                    <td align="left"><?php echo smarty_function_localize(array('str' => 'Avg. commission per impression (raw/unique)'), $this);?>
</td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                </tr>
                <tr>
                    <td align="left"><?php echo smarty_function_localize(array('str' => 'Avg. commission per sale'), $this);?>
</td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->commission->pending,'p2' => $this->_tpl_vars['sumTransaction']->count->pending), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->commission->declined,'p2' => $this->_tpl_vars['sumTransaction']->count->declined), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->commission->approved,'p2' => $this->_tpl_vars['sumTransaction']->count->approved), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->commission->paid,'p2' => $this->_tpl_vars['sumTransaction']->count->paid), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                </tr>
                <tr class="gray">
                    <td rowspan="2"><?php echo smarty_function_localize(array('str' => 'Commissions Multi-Tier'), $this);?>
</td>
                    <td align="center" rowspan="2"><?php echo smarty_function_localize(array('str' => 'Pending'), $this);?>
</td>
                    <td align="center" rowspan="2"><?php echo smarty_function_localize(array('str' => 'Declined'), $this);?>
</td>
                    <td align="center" colspan="2"><?php echo smarty_function_localize(array('str' => 'Approved'), $this);?>
</td>
                </tr>
                <tr class="gray">
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Unpaid'), $this);?>
</td>
                    <td align="center"><?php echo smarty_function_localize(array('str' => 'Paid'), $this);?>
</td>
                </tr>
                <?php echo $this->_tpl_vars['sumTransaction']->clear(); ?>

                <?php $_from = $this->_tpl_vars['transactionTypesTier']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['transactions']):
?>
                    <?php if ($this->_tpl_vars['transactions']->type != 'R' && $this->_tpl_vars['transactions']->type != 'H'): ?>
                        <?php echo $this->_tpl_vars['sumTransaction']->add($this->_tpl_vars['transactions']); ?>

                        <tr>
                            <td align="left" valign="top"><?php echo $this->_tpl_vars['transactions']->name; ?>
</td>
                            <td align="right" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->commission->pending)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                              <?php if ($this->_tpl_vars['transactions']->refunds->commission->pending != 0): ?> <br/><span class="NumberDataRed"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->refunds->commission->pending)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?>
                              <?php if ($this->_tpl_vars['transactions']->chargebacks->commission->pending != 0): ?> <br/><span class="NumberDataOrange"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->chargebacks->commission->pending)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?></td>
                            <td align="right" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->commission->declined)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                              <?php if ($this->_tpl_vars['transactions']->refunds->commission->declined != 0): ?> <br/><span class="NumberDataRed"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->refunds->commission->declined)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?>
                              <?php if ($this->_tpl_vars['transactions']->chargebacks->commission->declined != 0): ?> <br/><span class="NumberDataOrange"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->chargebacks->commission->declined)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?></td>
                            <td align="right" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->commission->approved)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                              <?php if ($this->_tpl_vars['transactions']->refunds->commission->approved != 0): ?> <br/><span class="NumberDataRed"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->refunds->commission->approved)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?>
                              <?php if ($this->_tpl_vars['transactions']->chargebacks->commission->approved != 0): ?> <br/><span class="NumberDataOrange"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->chargebacks->commission->approved)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?></td>
                            <td align="right" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->commission->paid)) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp)); ?>

                              <?php if ($this->_tpl_vars['transactions']->refunds->commission->paid != 0): ?> <br/><span class="NumberDataRed"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->refunds->commission->paid)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?>
                              <?php if ($this->_tpl_vars['transactions']->chargebacks->commission->paid != 0): ?> <br/><span class="NumberDataOrange"><?php echo ((is_array($_tmp=$this->_tpl_vars['transactions']->chargebacks->commission->paid)) ? $this->_run_mod_handler('currency', true, $_tmp) : smarty_modifier_currency($_tmp)); ?>
</span><?php endif; ?></td>
                        </tr>
                     <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>
                <tr>
                    <td align="left"><?php echo smarty_function_localize(array('str' => 'Avg. commission per click (raw/unique)'), $this);?>
</td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['clicks']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['clicks']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                </tr>
                <tr>
                    <td align="left"><?php echo smarty_function_localize(array('str' => 'Avg. commission per impression (raw/unique)'), $this);?>
</td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->pending,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->declined,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->approved,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['impressions']->count->raw), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span>
                                        /<span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->count->paid,'p2' => $this->_tpl_vars['impressions']->count->unique), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                </tr>
                <tr>
                    <td align="left"><?php echo smarty_function_localize(array('str' => 'Avg. commission per sale'), $this);?>
</td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->commission->pending,'p2' => $this->_tpl_vars['sumTransaction']->count->pending), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->commission->declined,'p2' => $this->_tpl_vars['sumTransaction']->count->declined), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->commission->approved,'p2' => $this->_tpl_vars['sumTransaction']->count->approved), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                    <td align="right"><span class="NumberData"><?php echo ((is_array($_tmp=smarty_function_ratio(array('p1' => $this->_tpl_vars['sumTransaction']->commission->paid,'p2' => $this->_tpl_vars['sumTransaction']->count->paid), $this))) ? $this->_run_mod_handler('currency_span', true, $_tmp) : smarty_modifier_currency_span($_tmp));?>
</span></td>
                </tr>
            </table>
            <br>
            <br>
            * <span class="NumberDataRed"><?php echo smarty_function_localize(array('str' => 'Refunds are marked with red color'), $this);?>
</span><br>
            ** <span class="NumberDataOrange"><?php echo smarty_function_localize(array('str' => 'Chargebacks are marked with orange color'), $this);?>
</span>  
        </div>
    </div>
</div>