jQuery(window).load(function(){

	jQuery('.sync-height').syncheight();

	// show/hide sidenav
	jQuery('#header .nav-btn').click(function(){
		jQuery(this).find('.mdi').toggleClass('mdi-close mdi-plus');
		jQuery('body').toggleClass('hide-side-nav');
		return false;
	});

	// focus mini search form
	jQuery('#search_mini_form input.input-text').focus(function(){
		jQuery('#search_mini_form').addClass('focus');
	}).blur(function(){
		jQuery('#search_mini_form').removeClass('focus');
	});

	// custom dropdown
	jQuery('.custom-dropdown strong').click(function(){
		jQuery('.custom-dropdown.focus strong').not(this).parent().removeClass('focus');
		jQuery(this).parent().toggleClass('focus');
	});

	// select overlay update label value
	jQuery('.select-overlay select').change(function() {
		jQuery(this).parents('.select-overlay').find('label').html(jQuery(this).val());
		jQuery(this).parents('form').submit();
	});

	// scroll to id
	jQuery('.scroll-to').click(function(){
		jQuery('html, body').animate({
			scrollTop: jQuery(jQuery(this).attr('href')).offset().top
		}, 500);
		return false;
	});

	// scroll 85% lower
	jQuery('.show-more').click(function(){
		jQuery('html, body').animate({
			scrollTop: jQuery(window).scrollTop() + (jQuery(window).height() * 0.85)
		}, 500);
		return false;
	});

});

jQuery(window).scroll(function() {

	// show back to top after 300 pixels scrolled
	if(jQuery(window).scrollTop() > 300) {
		jQuery('.back-to-top').addClass('show');
	} else {
		jQuery('.back-to-top').removeClass('show');
	}

	// hide show more when footer is reached
	if(jQuery(window).scrollTop() > (jQuery('#wrapper').height() - (jQuery('#footer').outerHeight()))) {
		jQuery('.show-more').addClass('hide');
	}

});

jQuery(window).resize(function(){
	jQuery('.sync-height').syncheight();
});