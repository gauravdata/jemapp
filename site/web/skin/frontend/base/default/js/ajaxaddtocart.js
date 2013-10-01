jQuery(function() {


    var productform = jQuery('#product_addtocart_form');

    if (productform.length > 0) {
        productform.submit(function(e) {
            e.preventDefault();
            $('product_addtocart_form').stopObserving('submit');
            if (this.action.indexOf('checkout/') > -1) {
                var action = this.action.replace('checkout/','ajaxaddtocart/');
                jQuery.post(action,jQuery(this).serialize(),function(data) {
                    jQuery('#added-to-cart').remove();

                    //enable addtocart button
                    productform.find('.btn-large').removeAttr('disabled');

                    //check if there is a message
                    var htmlfound = data.indexOf('<');
                    if (htmlfound != -1 && htmlfound < 10) {
                        jQuery('body').append(data);
                        jQuery('#added-to-cart').modal();
                    }
                    else {
                        var alertmessage = '<div class="modal hide fade">';
                        alertmessage += '<div class="alert alert-block alert-error" style="margin-bottom:0px;">';
                        alertmessage += '<button type="button" class="close" data-dismiss="modal">×</button>';
                        alertmessage += '<h4 class="alert-heading">Fout</h4>';
                        alertmessage += '<p>'+data+'</p>';
                        alertmessage += '<p><a class="btn" data-dismiss="modal" href="#">Sluiten</a></p>';
                        alertmessage += '</div></div>';
                        alertmessage = jQuery(alertmessage);
                        jQuery('body').append(alertmessage);
                        alertmessage.modal();
                    }
                });
                return false;
            }
            else {
                this.submit();
            }
            return true;
        });
    }
});