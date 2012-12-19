<?php /* Smarty version 2.6.18, created on 2011-08-17 13:00:55
         compiled from about.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'localize', 'about.tpl', 5, false),)), $this); ?>
<!-- about -->
<?php echo "<div id=\"ApplicationDetails\" class=\"ApplicationDetailsBox\"></div>"; ?>

<div class="AboutFieldset AboutFieldsetCredits">
	<div class="FieldsetLegend"><?php echo smarty_function_localize(array('str' => 'Credits'), $this);?>
</div>
	<div class="About">
		<div class="DevType">Affiliate system architect</div>
		<div class="DevNames">
			Maros Fric
		</div>
		<div class="DevType">Framework architects</div>
		<div class="DevNames">
			Andrej Harsani<br />
			Michal Bebjak
		</div>
		<div class="DevType">Developers</div>
		<div class="DevNames">
			Viktor Zeman<br />
			Milos Jancovic<br />
			Andrej Harsani<br />
			Michal Bebjak<br />
			Maros Fric
		</div>
		<div class="DevType">Designers</div>
		<div class="DevNames">
			Jan Perdoch<br />
			Matej Pliesovsky
		</div>
		<div class="DevType">Quality assurance</div>
		<div class="DevNames">
			Maros Fric<br />
			Michal Bebjak
		</div>
	</div>
</div>

<div class="AboutFieldset AboutFieldsetNews">
	<div class="FieldsetLegend"><?php echo smarty_function_localize(array('str' => 'News'), $this);?>
</div>
	<?php echo "<div id=\"rssBox\"></div>"; ?>
</div>

<div class="AboutFieldset AboutFieldsetFacebook">
	<div class="FieldsetLegend"><?php echo smarty_function_localize(array('str' => 'On facebook'), $this);?>
</div>
	<?php echo "<div id=\"facebookFrame\" class=\"FacebookBox\"></div>"; ?>
</div>

<div class="ClearBoth"></div>