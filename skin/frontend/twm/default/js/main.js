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
		jQuery(this).parent().toggleClass('focus');
	});

});