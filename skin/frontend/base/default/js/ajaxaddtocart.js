jQuery(function() {
    var productform = jQuery('#product_addtocart_form');

    if (productform.length > 0) {
        productform.submit(function(e) {
            e.preventDefault();

            $('product_addtocart_form').stopObserving('submit');

            var qtyInc = jQuery('input[name=qty_increments]').val();
            if (qtyInc) {
                var total = 0;
                jQuery('input.qty').each(function(index, item){
                    total += parseInt(jQuery(item).val());
                });
                if (total % qtyInc != 0) {
                    var alertmessage = '<div class="modal fade">';
                    alertmessage += '<div class="modal-dialog">';
                    alertmessage += '<div class="modal-content">';
                    alertmessage += '<div class="modal-header">';
                    alertmessage += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
                    alertmessage += '<h4 class="modal-title">Fout</h4>';
                    alertmessage += '</div>';
                    alertmessage += '<div class="modal-body">';
                    alertmessage += '<p>Dit product kan alleen in een veelvoud van '+qtyInc+' worden toegevoegd</p>';
                    alertmessage += '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Sluiten</button>';
                    alertmessage += '</div></div></div></div>';
                    alertmessage = jQuery(alertmessage);
                    jQuery('body').append(alertmessage);
                    alertmessage.modal();

                    return;
                }
            }
            if (this.action.indexOf('checkout/') > -1) {
                var action = this.action.replace('checkout/','ajaxaddtocart/');
                jQuery.post(action,jQuery(this).serialize(),function(data) {
                    jQuery('#added-to-cart').remove();

                    //enable addtocart button
                    //productform.find('.btn-large').removeAttr('disabled');

                    //check if there is a message
                    var htmlfound = data.indexOf('<');
                    if (htmlfound != -1 && htmlfound < 10) {
                        jQuery('body').append(data);
                        jQuery('#added-to-cart').modal();
                    }
                    else {
                        var alertmessage = '<div class="modal fade">';
						alertmessage += '<div class="modal-dialog">';
						alertmessage += '<div class="modal-content">';
						alertmessage += '<div class="modal-header">';
						alertmessage += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
						alertmessage += '<h4 class="modal-title">Fout</h4>';
						alertmessage += '</div>';
						alertmessage += '<div class="modal-body">';
                        alertmessage += '<p>'+data+'</p>';
						alertmessage += '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Sluiten</button>';
                        alertmessage += '</div></div></div></div>';
                        alertmessage = jQuery(alertmessage);
                        jQuery('body').append(alertmessage);
                        alertmessage.modal();
                    }
                });
                return false;
            }
            else {
                if (typeof AEC !== 'undefined')
                    AEC.ajax(this, dataLayer);
                else
                    this.submit();
            }
            return true;
        });
    }
});