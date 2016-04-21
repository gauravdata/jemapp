jQuery(window).load(function(){
	jQuery('.sync-height').syncheight();

	// show/hide sidenav
	if(Cookies.get('side-nav') != 'open') {
		jQuery('body').addClass('hide-side-nav');
	}
	jQuery('#header .nav-btn').click(function(){
		jQuery('body').toggleClass('hide-side-nav');

		if(jQuery('body').hasClass('hide-side-nav')) {
			Cookies.set('side-nav', 'closed');
		} else {
			Cookies.set('side-nav', 'open');
		}
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
	jQuery('.checkout-cart-index .select-overlay select').change(function() {
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

	// set original toolbar top for scroll
	var toolbar = jQuery('.category-products > .toolbar');
	if(toolbar.length > 0) {
		toolbar.data('top', jQuery('.category-products > .toolbar').offset().top);
	}

	// show cart on hover
	jQuery('.top-link-cart, #wrapper > .block.block-cart').mouseenter(function() {
		if(jQuery('#wrapper > .block.block-cart .block-content').length > 0) {
			jQuery('#wrapper > .block.block-cart').addClass('show');
		}
	}).mouseleave(function(){
		jQuery('main > .block.block-cart').removeClass('show');
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

	// scroll category toolbar
	var toolbar = jQuery('.category-products > .toolbar');
	if((jQuery(window).scrollTop() + jQuery('#header').height()) > toolbar.data('top')) {
		toolbar.addClass('fixed');
		jQuery('.amshopby-filters').addClass('fixed');
	} else {
		toolbar.removeClass('fixed');
		jQuery('.amshopby-filters').removeClass('fixed');
	}

});

jQuery(window).resize(function(){
	jQuery('.sync-height').syncheight();
});
