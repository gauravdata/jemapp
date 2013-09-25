<?php /* Smarty version 2.6.18, created on 2011-08-17 14:21:53
         compiled from afflink_protector_screen.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'afflink_protector_screen.tpl', 3, false),)), $this); ?>
<!-- afflink_protector_screen -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Affiliate Link Protector/Cloaker'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'AffLinkProtectorInnerDescription'), $this);?>

</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'Generate Cloaked File'), $this);?>
</legend>
<table>
    <tr>
        <td class="AffiliateLinkProtectorURLLabel"><?php echo smarty_function_localize(array('str' => 'URL to protect / cloak'), $this);?>
</td><td class="AffiliateLinkProtectorURL"><?php echo "<div id=\"urlToProtect\"></div>"; ?></td>
    </tr>
    <tr>
        <td><?php echo smarty_function_localize(array('str' => 'Redirection type'), $this);?>
</td><td><?php echo "<div id=\"redirectionType\"></div>"; ?></td>
    </tr>
    <tr>
        <td></td><td><?php echo "<div id=\"generateButton\"></div>"; ?></td>
    </tr>
</table>
</fieldset>

<fieldset>
<?php echo smarty_function_localize(array('str' => 'AffLinkProtectorHowToUse'), $this);?>

</fieldset>