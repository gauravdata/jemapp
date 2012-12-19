<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from signup_commissions.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'signup_commissions.tpl', 4, false),)), $this); ?>
<!--    signup_commissions  -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Signup commission'), $this);?>
</legend>
    <?php echo smarty_function_localize(array('str' => 'Assign to new affiliates signup bonus, to motivate them to enter your affiliate program.'), $this);?>

    <table>
        <tr>
            <td><?php echo "<div id=\"signupBonus\"></div>"; ?></td>
            <td><?php echo "<div id=\"saveButton\"></div>"; ?></td>
        </tr>
    </table>
</fieldset>

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Referral commission'), $this);?>
</legend>
    <?php echo smarty_function_localize(array('str' => 'With referral commissions you can motivate your current affiliates to recruite new affiliates for you.'), $this);?>

    <?php echo "<div id=\"referralCommissions\"></div>"; ?>
</fieldset>