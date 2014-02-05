function setFooter() {
	var footerHeight = jQuery('footer').outerHeight();
	jQuery('#main-container').css({
		paddingBottom: footerHeight + 30
	});
}

jQuery(window).resize(function(){
	jQuery('.sync-height').syncheight();
	setFooter();
});
jQuery(window).load(function(){
	jQuery('.sync-height').syncheight();
	setFooter();
	jQuery('fieldset legend').contents().unwrap().wrap('<div class="legend"/>');

	jQuery('.navbar-default .navbar-nav > li.dropdown-link').mouseenter(function(){
		jQuery(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn();
	}).mouseleave(function(){
		jQuery(this).find('.dropdown-menu').stop(true, true).delay(500).fadeOut();
	});
});