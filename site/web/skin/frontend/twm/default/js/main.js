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
});