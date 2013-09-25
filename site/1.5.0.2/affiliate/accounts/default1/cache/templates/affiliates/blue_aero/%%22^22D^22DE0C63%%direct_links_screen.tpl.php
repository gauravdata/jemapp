<?php /* Smarty version 2.6.18, created on 2011-12-16 03:39:02
         compiled from direct_links_screen.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'direct_links_screen.tpl', 2, false),)), $this); ?>
<!-- direct_links_screen -->
<?php echo smarty_function_localize(array('str' => 'Read more about DirectLinks'), $this);?>
 <a href='#Custom-Page;%7B"template":"custom/directlink_explained"%7D' style="text-decoration: underline; font-weight: bold; color:#135fab"><?php echo smarty_function_localize(array('str' => 'here'), $this);?>
</a>.
<div>
  <div style="float: left"><?php echo smarty_function_localize(array('str' => 'You don\'t need to enter each and every URL address of your pages, you can use star convention.'), $this);?>
<br/>
<?php echo smarty_function_localize(array('str' => 'So for example pattern'), $this);?>
 <strong>*yoursite.com*</strong> <?php echo smarty_function_localize(array('str' => 'will match:'), $this);?>
<br/>
www.yoursite.com<br/>
subdomain.yoursite.com<br/>
www.yoursite.com/something.html<br/>
www.yoursite.com/dir/something.php?parameters<br/>
  </div>
  <div style="float: left">
  
<fieldset>
    <legend><?php echo smarty_function_localize(array('str' => 'Test URL matching'), $this);?>
</legend>
    
    <div class="HintText"><?php echo smarty_function_localize(array('str' => 'You can test if your pattern matches the given URL.'), $this);?>
</div>
        
    <div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Pattern'), $this);?>
</div>
    <?php echo "<div id=\"pattern\" class=\"FormFieldBigInline FormFieldOnlyInput\"></div>"; ?>
    <div class="clear"></div>    
    <div class="Inliner"><?php echo smarty_function_localize(array('str' => 'Real url'), $this);?>
</div>
    <?php echo "<div id=\"realUrl\" class=\"FormFieldBigInline FormFieldOnlyInput\"></div>"; ?>
     <div class="clear"></div>
    <?php echo "<div id=\"checkButton\"></div>"; ?>
    <?php echo "<div id=\"message\"></div>"; ?>
</fieldset>
  </div>
</div>
<div class="clear"></div> 
<br/>
<?php echo smarty_function_localize(array('str' => 'Links may not be changed. If you want to change link, you can delete the old link and create new, which must merchant again approve'), $this);?>

<?php echo "<div id=\"UrlsGrid\"></div>"; ?>