<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:43
         compiled from traffic_info.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'traffic_info.tpl', 6, false),)), $this); ?>
<!-- traffic_info -->
<table class="StatsSummaries">
<tbody>
        <tr class="gray">
            <td> </td>
            <td><?php echo smarty_function_localize(array('str' => 'Used'), $this);?>
</td>
            <td><?php echo smarty_function_localize(array('str' => 'Limit'), $this);?>
</td>           
        </tr>
        <tr class="light">
            <td><?php echo smarty_function_localize(array('str' => 'Transactions'), $this);?>
 <?php echo "<div id=\"transactionDates\"></div>"; ?></td>
            <td><?php echo "<div id=\"transactionsUsed\"></div>"; ?></td>
            <td><?php echo "<div id=\"transactionsLimit\"></div>"; ?></td>            
        </tr>
        <tr class="dark">
            <td><?php echo smarty_function_localize(array('str' => 'Bandwidth'), $this);?>
 <?php echo "<div id=\"bandwidthDates\"></div>"; ?></td>
            <td><?php echo "<div id=\"bandwidthUsed\"></div>"; ?></td>
            <td><?php echo "<div id=\"bandwidthLimit\"></div>"; ?></td>            
        </tr>
</tbody>        
</table>
<br/>
<table class="StatsSummaries">
<tbody>
        <tr class="gray">
            <td><?php echo smarty_function_localize(array('str' => 'Bandwidth'), $this);?>
</td>
            <td><?php echo smarty_function_localize(array('str' => 'Transactions'), $this);?>
</td>
        </tr>
        <tr class="gray">
            <td><div class="TrafficInfoGraph">
<?php echo "<div id=\"bandwidthChart\"></div>"; ?>
</div></td>
            <td><div class="TrafficInfoGraph">
<?php echo "<div id=\"transactionChart\"></div>"; ?>
</div>
</td>
        </tr>
</tbody>
</table>