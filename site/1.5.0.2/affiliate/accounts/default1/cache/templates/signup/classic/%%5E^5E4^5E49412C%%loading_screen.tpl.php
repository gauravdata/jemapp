<?php /* Smarty version 2.6.18, created on 2011-08-18 03:20:22
         compiled from loading_screen.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'loading_screen.tpl', 7, false),)), $this); ?>
<!-- loading_screen -->
<div id="Container">

    <div id="Content"> 
    
		<div class="LoadingBox">
			<div class="LoadingInfo"><?php echo smarty_function_localize(array('str' => 'Loading application'), $this);?>
</div>
		</div>	
		
    </div>  

</div>