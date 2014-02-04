jQuery(function(){
	var itemId = 0
	jQuery('.navbar ul.nav li.parent > a').each(function(){
		jQuery(this).data('toggle', 'dropdown').data('target', '#').removeClass('dropdown').addClass('dropdown-toggle').attr('id', 'item-' + itemId);
		itemId++;
	});
	var dropdownId = 0
	jQuery('.navbar ul.nav li.parent ul').each(function(){
		jQuery(this).addClass('dropdown-menu').attr('aria-labelledby', 'item-' + dropdownId);
		dropdownId++;
	});

	jQuery('.navbar ul.nav li.parent').mouseenter(function(){
		jQuery(this).find('> .dropdown-menu').stop(true, true).delay(100).fadeIn();
	}).mouseleave(function(){
		jQuery(this).find('> .dropdown-menu').stop(true, true).delay(500).fadeOut();
	});
});