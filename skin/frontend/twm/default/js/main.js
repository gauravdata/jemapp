Validation.defaultOptions.immediate = true;

// show cart on hover
jQuery(document).on( "mouseenter", ".top-link-cart, #wrapper > .block.block-cart", function() {
	jQuery('#wrapper > .block.block-cart').addClass('show');
}).on( "mouseleave", ".top-link-cart, #wrapper > .block.block-cart", function() {
	jQuery('#wrapper > .block.block-cart').removeClass('show');
});

jQuery(window).load(function(){
	jQuery('.sync-height').syncheight();

	// remove demo notice after 2.5 seconds
	setTimeout(function(){
		jQuery('.demo-notice').fadeOut();
	}, 2500);

	// show/hide sidenav
	jQuery('#header .nav-btn').click(function(){
		jQuery('body').toggleClass('hidden-side-nav open-side-nav');

		if(jQuery('body').hasClass('hidden-side-nav')) {
			Cookies.set('side-nav', 'closed');
		} else {
			Cookies.set('side-nav', 'open');
		}
		return false;
	});
	if(jQuery(window).width() < 1250) {
		Cookies.set('side-nav', 'closed');
		jQuery('body').addClass('hidden-side-nav').removeClass('open-side-nav');
	}

	// focus mini search form
	jQuery('#search_mini_form input.input-text').focus(function(){
		jQuery('#search_mini_form').addClass('focus');
	}).blur(function(){
		jQuery('#search_mini_form').removeClass('focus');
	});

	// click label mini search form
	jQuery('#search_mini_form label').click(function(){
		if(jQuery('#search_mini_form').find('input.input-text').val() !=  '') {
			jQuery('#search_mini_form').submit();
		} else {
			if(jQuery(window).width() < 1250) {
				jQuery('#search_mini_form .input-box').toggleClass('show');
			}
		}
	});

	// custom dropdown
	jQuery('.custom-dropdown strong').click(function(){
		jQuery('.custom-dropdown.focus strong').not(this).parent().removeClass('focus');
		jQuery(this).parent().toggleClass('focus');
	});

	// select overlay update label value
	jQuery('.select-overlay select').each(function() {
		var label = jQuery(this).parents('.select-overlay').find('label');
		if(jQuery(this).val() != '' && label.find('span').length < 1) {
			label.html(jQuery(this).find('option:selected').html());
		}
	});
	jQuery('.select-overlay select').change(function() {
		jQuery(this).parents('.select-overlay').find('label').html(jQuery(this).find('option:selected').html());
		if(jQuery(this).hasClass('qty')) {
			jQuery(this).parents('form').submit();
		}
	});

	// scroll to id
	jQuery('.scroll-to').click(function(){
		scrollToId(jQuery(this).attr('href'));
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

	// remove title attribute from top link cart
	jQuery('.top-link-cart').attr('title', '');

	// checkbox fix
	jQuery('input[type=checkbox]').click(function() {
		jQuery(this).parent('.input-box').toggleClass('checkbox-checked');
	});
	
	// show footer nav
	jQuery('.footer-nav h2').click(function(){
		jQuery(this).parent().toggleClass('show');
	});

	// wrap tables
	jQuery('table').wrap('<div class="responsive-table"></div>');

	// show modal
	jQuery('.show-modal').click(function() {
		jQuery(jQuery(this).attr('href')).addClass('show-modal');
		jQuery('body').addClass('modal-open')
		return false;
	});

	// close modal
	jQuery('.modal-close, .modal-click-area').click(function() {
		jQuery('.modal-wrapper').removeClass('show-modal');
		jQuery('body').removeClass('modal-open')
		return false;
	});

	// go back 1
	jQuery('.product-back-btn').click(function() {
		window.history.back(-1);
		return false;
	});

	// show nav children
	jQuery('#side-nav > ul > li.parent').each(function(){
		var $this = jQuery(this);
		if($this.hasClass('active')) {
			$this.addClass('open')
		} else {
			$this.addClass('closed')
		}
	});
	jQuery('#side-nav > ul > li.parent > a').click(function(){
		var $this = jQuery(this);
		$this.next().slideToggle();
		$this.parent().toggleClass('closed open');
		return false;
	});

});

var lastScrollTop = 0;

jQuery(window).scroll(function() {

	// hide show more label after 100 pixels scolled
	if(jQuery(window).scrollTop() > 100) {
		jQuery('.show-more').addClass('hidden-label');
	}

	// show back to top after 300 pixels scrolled
	if(jQuery(window).scrollTop() > 300) {
		jQuery('.back-to-top').addClass('show');
	} else {
		jQuery('.back-to-top').removeClass('show');
	}

	// hide menu after 400 pixels scrolled and scroll down
	var st = jQuery(this).scrollTop();
	if (st > lastScrollTop){
		if(jQuery(window).scrollTop() > 400) {
			jQuery('#header').addClass('hide');
			jQuery('.toolbar').addClass('top');
		}
	} else {
		jQuery('#header').removeClass('hide');
		jQuery('.toolbar').removeClass('top');
	}
	lastScrollTop = st;

	// hide show more when footer is reached
	if(jQuery(window).scrollTop() > (jQuery('#wrapper').height() - (jQuery('#footer').outerHeight()))) {
		jQuery('.show-more').addClass('hide');
	}

	// scroll category toolbar
	var toolbar = jQuery('.category-products > .toolbar');
	if((jQuery(window).scrollTop() + jQuery('#header').height()) > toolbar.data('top')) {
		toolbar.addClass('fixed');
	} else {
		toolbar.removeClass('fixed');
	}

});

jQuery(window).resize(function(){
	jQuery('.sync-height').syncheight();

	// hide or show side nav based in window size
	if(jQuery(window).width() < 1250) {
		Cookies.set('side-nav', 'closed');
		jQuery('body').addClass('hidden-side-nav').removeClass('open-side-nav');
	} else {
		Cookies.set('side-nav', 'open');
		jQuery('body').removeClass('hidden-side-nav').addClass('open-side-nav');
	}
});

function scrollToId(scrollId) {
	jQuery('html, body').animate({
		scrollTop: jQuery(scrollId).offset().top
	}, 500);
	return false;
}