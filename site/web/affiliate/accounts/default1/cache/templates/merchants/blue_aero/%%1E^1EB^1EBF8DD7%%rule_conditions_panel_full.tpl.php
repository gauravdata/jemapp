<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from rule_conditions_panel_full.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'rule_conditions_panel_full.tpl', 4, false),)), $this); ?>
<!--    rule_conditions_panel_full      -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Conditions'), $this);?>
</legend>
    <table>
        <tr>
            <td><?php echo smarty_function_localize(array('str' => 'If'), $this);?>
</td>
            <td width="170"><?php echo "<div id=\"what\" class=\"ConditionListBox\"></div>"; ?></td>
            <td width="45"><?php echo smarty_function_localize(array('str' => 'that are'), $this);?>
</td>
            <td><?php echo "<div id=\"status\" class=\"ConditionListBox\"></div>"; ?></td>
            <td></td>
        </tr>
        <tr>
            <td><?php echo smarty_function_localize(array('str' => 'in'), $this);?>
</td>
            <td><?php echo "<div id=\"date\" class=\"ConditionListBox\"></div>"; ?></td>
            <td></td>
            <td><?php echo "<div id=\"since\" class=\"ConditionValue\"></div>"; ?></td>
            <td></td>
        </tr>
        <tr>
            <td><?php echo smarty_function_localize(array('str' => 'is'), $this);?>
</td>
            <td><?php echo "<div id=\"equation\" class=\"ConditionListBox\"></div>"; ?></td>
            <td></td>
            <td><?php echo "<div id=\"equationvalue1\" class=\"ConditionValue\"></div>"; ?></td>
            <td><?php echo "<div id=\"equationvalue2\" class=\"ConditionValue\"></div>"; ?></td>
        </tr>
    </table>
</fieldset>