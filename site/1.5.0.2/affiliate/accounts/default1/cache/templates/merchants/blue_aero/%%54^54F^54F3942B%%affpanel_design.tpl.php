<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:06
         compiled from affpanel_design.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'affpanel_design.tpl', 2, false),)), $this); ?>
<!-- affpanel_design -->
<h3><?php echo smarty_function_localize(array('str' => 'Customize design of affiliate panel'), $this);?>
</h3>
<?php echo smarty_function_localize(array('str' => 'Here you can change design of affiliate panel by changing it\'s templates.'), $this);?>


<div class="designForm">

<div class="designHeader">
<div class="designLogo"><?php echo "<div id=\"ChangeLogo\"></div>"; ?></div>
<?php echo smarty_function_localize(array('str' => 'HEADER'), $this);?>
<br/><?php echo "<div id=\"EditHeader\"></div>"; ?>
</div>

<div class="designMenu"><?php echo smarty_function_localize(array('str' => 'MENU'), $this);?>
<br/><?php echo "<div id=\"EditMenu\"></div>"; ?></div>

<div class="designContent2">
      <div class="designHeaderContent">
        <?php echo smarty_function_localize(array('str' => 'Affiliate link: http://www.example..'), $this);?>
<?php echo "<div id=\"EditContentHeader\"></div>"; ?>
      </div>
    <?php echo smarty_function_localize(array('str' => 'CONTENT'), $this);?>
</div>



<div class="designFooter"><?php echo smarty_function_localize(array('str' => 'FOOTER'), $this);?>
 <br/><?php echo "<div id=\"EditFooter\"></div>"; ?></div>
</div>