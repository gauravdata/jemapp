<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from affiliate_menu_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affiliate_menu_settings.tpl', 4, false),)), $this); ?>
<!-- affiliate_menu_settings -->
<div class="AffiliateMenu">
    <fieldset>
        <legend><?php echo smarty_function_localize(array('str' => 'Affiliate menu'), $this);?>
</legend>
        <?php echo smarty_function_localize(array('str' => 'You can modify affiliate menu by dragging it\'s items.'), $this);?>
<br/>
        <?php echo smarty_function_localize(array('str' => 'Items can be removed by dragging to Trash'), $this);?>

        <hr>
        <?php echo "<div id=\"menuTree\"></div>"; ?>
        <hr>
        <?php echo "<div id=\"Trash\"></div>"; ?>
        <?php echo "<div id=\"WarningMessage\"></div>"; ?>
        <?php echo "<div id=\"SaveButton\"></div>"; ?>
    </fieldset>
</div>

<div class="clear"/>