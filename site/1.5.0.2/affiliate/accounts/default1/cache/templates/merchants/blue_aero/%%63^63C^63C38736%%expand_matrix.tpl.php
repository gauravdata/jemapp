<?php /* Smarty version 2.6.18, created on 2011-08-17 13:01:49
         compiled from expand_matrix.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'expand_matrix.tpl', 13, false),)), $this); ?>
<!--	expand_matrix	-->

<table>
    <tr>
    	<td><?php echo "<div id=\"matrixExpandWidthLabel\"></div>"; ?></td>
        <td><?php echo "<div id=\"matrixExpandWidthInput\"></div>"; ?><?php echo "<div id=\"matrixExpandWidthError\"></div>"; ?></td>
    </tr>
    <tr>
    	<td><?php echo "<div id=\"matrixExpandHeightLabel\"></div>"; ?></td>
        <td><?php echo "<div id=\"matrixExpandHeightInput\"></div>"; ?><?php echo "<div id=\"matrixExpandHeightError\"></div>"; ?></td>
    </tr>
</table>
<?php echo smarty_function_localize(array('str' => 'affiliate will be saved as a child to his original referring affiliate and the matrix of original referring affiliate will be expanded'), $this);?>
