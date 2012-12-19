<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from rule_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'rule_panel.tpl', 19, false),)), $this); ?>
<!--    rule_panel      -->

<div class="ScreenHeader RuleViewHeader">
    <div class="RuleRightIcons">  
        <?php echo "<div id=\"RefreshButton\"></div>"; ?>  
    </div>
    <div class="ScreenTitle">
        <?php echo "<div id=\"screenTitle\"></div>"; ?>
    </div>
    <div class="ScreenDescription">
       <?php echo "<div id=\"screenDescription\"></div>"; ?>
    </div>
    <div class="clear"/>
</div>

<?php echo "<div id=\"ruleConditions\"></div>"; ?>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Actions'), $this);?>
</legend>
    <table>
        <tr>
            <td><?php echo smarty_function_localize(array('str' => 'then'), $this);?>
</td>
            <td width="250"><?php echo "<div id=\"action\" class=\"ConditionListBox\"></div>"; ?></td>
            <td><?php echo "<div id=\"commissiongroupid\" class=\"ConditionListBox\"></div>"; ?></td>
            <td><?php echo "<div id=\"bonustype\" class=\"ActionBonusType\"></div>"; ?></td>
            <td><?php echo "<div id=\"bonusvalue\" class=\"ActionBonusValue\"></div>"; ?></td>
        </tr>
    </table>
</fieldset>
<?php echo "<div id=\"formmessage\"></div>"; ?>
<?php echo "<div id=\"sendButton\"></div>"; ?>
<?php echo "<div id=\"cancelButton\"></div>"; ?>