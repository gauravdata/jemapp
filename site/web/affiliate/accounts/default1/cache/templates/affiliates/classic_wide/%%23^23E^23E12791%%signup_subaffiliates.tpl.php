<?php /* Smarty version 2.6.18, created on 2011-08-17 14:22:35
         compiled from signup_subaffiliates.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'signup_subaffiliates.tpl', 3, false),)), $this); ?>
<!-- signup_subaffiliates -->
<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'SubSignupOverview'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'SubSignupOverviewDescription'), $this);?>


<p/>
<?php echo "<div id=\"signupLink\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'SubSignupDownloadForms'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'SubSignupDownloadFormsDescription'), $this);?>


<p/>
<?php echo "<div id=\"downloadJoinForm\"></div>"; ?>
<?php echo "<div id=\"downloadLoginForm\"></div>"; ?>
</fieldset>

<fieldset>
<legend><?php echo smarty_function_localize(array('str' => 'SubSignupStats'), $this);?>
</legend>
<?php echo smarty_function_localize(array('str' => 'Number of your direct subaffiliates:'), $this);?>
 <?php echo "<div id=\"numberOfSubaffiliates\"></div>"; ?>

<table>
<tr>
  <td align="center"><?php echo "<div id=\"SubaffiliateSaleStats\"></div>"; ?></td>
  <td align="center"><?php echo "<div id=\"SubaffiliatesTree\"></div>"; ?></td>
</tr>
</table> 
</fieldset>