<?php /* Smarty version 2.6.18, created on 2011-08-17 13:02:32
         compiled from report_problems.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'report_problems.tpl', 3, false),)), $this); ?>
<!-- report_problems -->
<p>
<?php echo smarty_function_localize(array('str' => 'You can resolve your problem faster as we can answer your ticket by searching for solution in our knowledgebase:'), $this);?>
 
<b><a href="<?php echo $this->_tpl_vars['postAffiliateProHelp']; ?>
" target="_blank"><?php echo smarty_function_localize(array('str' => 'Click here to open Knowledgebase.'), $this);?>
</a></b>
</p>

<p>
<?php echo smarty_function_localize(array('str' => 'Would you like to report bug ? Please check first, if bug was not resolved already. List of resolved bugs you can find in our change log.'), $this);?>

<b><a href="<?php echo $this->_tpl_vars['qualityUnitChangeLog']; ?>
" target="_blank"><?php echo smarty_function_localize(array('str' => 'Click here to open Change log.'), $this);?>
</a></b>
</p>

<fieldset class="ReportProblems">
<legend><?php echo smarty_function_localize(array('str' => 'Report problem'), $this);?>
</legend>
<?php echo "<div id=\"email\"></div>"; ?>
<?php echo "<div id=\"subject\"></div>"; ?>
<?php echo "<div id=\"message\" class=\"ReportProblemsMessage\"></div>"; ?>
<?php echo "<div id=\"FormMessage\"></div>"; ?>
<?php echo "<div id=\"SendButton\"></div>"; ?>
</fieldset>