jQuery(window).load(function(){

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

	// switch product image
	jQuery('.product-img-box .more-views a').click(function(){
		var imageUrl = jQuery(this).attr('href');
		jQuery('.product-visual').css('background-image', 'url(' + imageUrl + ')');
		return false;
	});

	// select overlay update label value
	jQuery('.select-overlay select').change(function() {
		jQuery(this).parents('.select-overlay').find('label').html(jQuery(this).val());
	});

});