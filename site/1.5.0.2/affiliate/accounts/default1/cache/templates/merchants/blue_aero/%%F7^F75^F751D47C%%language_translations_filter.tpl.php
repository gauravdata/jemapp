<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:00
         compiled from language_translations_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'language_translations_filter.tpl', 4, false),)), $this); ?>
<!-- language_translations_filter -->
<div class="LanguageTranslationsFilter">
    <fieldset class="Filter">
        <legend><?php echo smarty_function_localize(array('str' => 'Message'), $this);?>
</legend>
        <div class="Resize">
            <?php echo smarty_function_localize(array('str' => 'Source'), $this);?>
 <?php echo "<div id=\"source\"></div>"; ?>
            <?php echo smarty_function_localize(array('str' => 'Translation'), $this);?>
 <?php echo "<div id=\"translation\"></div>"; ?>
        </div>
    </fieldset>

    <fieldset class="Filter">
       <legend><?php echo smarty_function_localize(array('str' => 'Status'), $this);?>
</legend>
       <div class="Resize">
       <?php echo "<div id=\"status\"></div>"; ?>
       </div>
    </fieldset>
   
    <fieldset class="Filter">
       <legend><?php echo smarty_function_localize(array('str' => 'Type'), $this);?>
</legend>
       <div class="Resize">
       <?php echo "<div id=\"type\"></div>"; ?>
       </div>
    </fieldset>
   
    <fieldset class="Filter">           
       <legend><?php echo smarty_function_localize(array('str' => 'Module'), $this);?>
</legend>
       <div class="Resize">
       <?php echo "<div id=\"module\"></div>"; ?>
       </div>
    </fieldset>
   
    <fieldset class="Filter">
       <legend><?php echo smarty_function_localize(array('str' => 'Is custom'), $this);?>
</legend>
       <div class="Resize">
       <?php echo "<div id=\"customer\"></div>"; ?>
       </div>
    </fieldset>
</div>
<div style="clear: both;"></div>