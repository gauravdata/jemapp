function setHeaderAndFooter() {
	var headerHeight = jQuery('header').outerHeight();
	var footerHeight = jQuery('footer').outerHeight();
	jQuery('#main-container').css({
		paddingTop: headerHeight,
		paddingBottom: footerHeight + 30
	});
}

jQuery(window).resize(function(){
	jQuery('.sync-height').syncheight();
	setHeaderAndFooter();
});
jQuery(window).load(function(){
	jQuery('.sync-height').syncheight();
	setHeaderAndFooter();
	jQuery('fieldset legend').contents().unwrap().wrap('<div class="legend"/>');
});