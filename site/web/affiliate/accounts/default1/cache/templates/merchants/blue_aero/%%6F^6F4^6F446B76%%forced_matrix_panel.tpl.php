<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from forced_matrix_panel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'forced_matrix_panel.tpl', 4, false),)), $this); ?>
<!--    forced_matrix_panel     -->

<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Forced matrix'), $this);?>
</legend>
    <?php echo smarty_function_localize(array('str' => 'Forced matrix allows you to limit to the number of referrals of subaffiliates any affiliate can refer. It means that every affiliate can have only specified number of children.'), $this);?>

    <br/>
    <?php echo smarty_function_localize(array('str' => 'Read more details about Forced Matrix in our knowledgebase:'), $this);?>
 <a href="<?php echo $this->_tpl_vars['knowledgebaseUrl']; ?>
features-and-functions/forced-matrix.html"><?php echo smarty_function_localize(array('str' => 'Forced Matrix description'), $this);?>
</a>
    <?php echo "<div id=\"matrixWidth\"></div>"; ?>
    <?php echo "<div id=\"matrixHeight\"></div>"; ?>
    <?php echo "<div id=\"fullForcedMatrix\"></div>"; ?>
    <?php echo "<div id=\"spillover\"></div>"; ?>
    <?php echo "<div id=\"spilloverAffiliate\"></div>"; ?>
    <?php echo "<div id=\"matrix_1_FillBonus\"></div>"; ?>
    <div class="MultiFillBonus">
    	<?php echo "<div id=\"matrixMultiFillBonus\"></div>"; ?>
    	<?php echo "<div id=\"matrixOtherFillBonus\"></div>"; ?>
    </div>    
</fieldset>