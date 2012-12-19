<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from get_pdf.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'get_pdf.tpl', 3, false),)), $this); ?>
<!--    get_pdf     -->
<fieldset class="Banner">
  <legend><?php echo smarty_function_localize(array('str' => 'Preview'), $this);?>
</legend>
    <div style="float:left">
        <?php echo smarty_function_localize(array('str' => 'Affiliate'), $this);?>

        <?php echo "<div id=\"affiliate\"></div>"; ?>
    </div>
    <div style="float:left">
        <?php echo "<div id=\"infoMessageLabel\"></div>"; ?>
    </div>
    <div style="float:left">
        <?php echo "<div id=\"showPreview\"></div>"; ?>
    </div>
    <div style="clear: both"></div>
</fieldset>