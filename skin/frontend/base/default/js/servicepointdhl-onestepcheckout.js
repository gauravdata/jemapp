var map = null;
var markers = [];
var geocoder = null;
var shippingmethodprefix = '';

function initMap(containerId, data, moretext, callback) {
    jQuery('#'+containerId).css('height', 250);

    var mapOptions = {
        zoom: 2,
        center: new google.maps.LatLng(0, 0),
        disableDefaultUI: true,
        panControl: true,
        zoomControl: true,
        scaleControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById(containerId), mapOptions);

    //remove previous markers
    for (var i in markers) {
        if (!isNaN(i)) {
            markers[i].setMap(null);
        }
    }

    var bounds = new google.maps.LatLngBounds();

    //store location data
    for (var i in data) {
        if (data.hasOwnProperty(i)) {

            geocodeAddress(data[i].Street + ' '+data[i].City+' '+data[i].Country,data[i], function(location, item) {
                var marker = createMarker(map,item,item.Name,location, moretext,callback);
                markers.push(marker);

                bounds.extend(location);
                map.fitBounds(bounds);
            });
        }
    }
}

function geocodeAddress(address,item,callback) {
    if (item.Lng && item.Lat) {
        callback(new google.maps.LatLng(item.Lat,item.Lng),item);
    }
    else {
        if (geocoder === null) {
            geocoder = new google.maps.Geocoder();
        }

        geocoder.geocode( {
            'address':address
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var location = results[0].geometry.location;
                callback(location,item);
            }else {
                //console.log(address,'not found');
            }
        });
    }
}

function createMarker(map, item, title, latlng, moretext, callback) {
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: title,
        animation: google.maps.Animation.DROP
    });

    createInfoWindow(marker,item,title,moretext,callback);

    google.maps.event.addListener(marker, "click", function(e) {
        marker.infowindow.close();
        marker.infowindow.open(map,marker);
    });
    return marker;
}

function createInfoWindow(marker,item,title,moretext,callback) {
    if (!moretext) {
        moretext = 'selecteer afhaalpunt';
    }

    if (typeof marker.infowindow == "undefined") {
        var windowContent = jQuery("<div><strong>"+title+"</strong><br />"+item.Street+"<br />"+item.Postcode+" "+item.City+"<br /><a class='btn' href='javascript:;' data-dismiss='modal'>"+moretext+" &raquo;</a></div>");
        //more link, scrolls to div
        if (!callback) {
            callback = function() {
                var base_url = onestepcheckout_login_popup.options.login_url.replace('onestepcheckout/ajax/login/', '');
                var url_save_billing = base_url + '/onestepcheckout/ajax/save_billing/';
                var url_set_methods = base_url + '/onestepcheckout/ajax/set_methods_separate/';

                var params = '?servicepointdhl_postcode=' + item.Postcode;
                params += '&servicepointdhl_city='  + item.City;
                params += '&servicepointdhl_id='  + item.Id;
                params += '&servicepointdhl_street='  + item.Street;

                get_save_billing_function(url_save_billing + params, url_set_methods + params)(item.Id);
            }
        }
        windowContent.find("a.btn").click(function() {
            callback(item.Id);
        });

        marker.infowindow = new google.maps.InfoWindow({
            content: windowContent[0]
        });

        marker.windowContent = windowContent;
    }
}

function searchServicepointDHL(button, formid, mapid, prefix, url, moretext, callback) {
    shippingmethodprefix = prefix;
    jQuery(button).addClass('disabled');

    var parameters = $(formid).serialize(true);
    if ($(formid).longlat) {
        parameters['longitude'] = $(formid).longlat.lng();
        parameters['latitude'] = $(formid).longlat.lat();
    }
    jQuery.post(url, parameters, function(data) {
        initMap(mapid, data, moretext, callback);

        jQuery(button).removeClass('disabled');
    }, "json");

}

function updateLongLatForField(field) {
    var form = field.form;
    if (field.value != '') {
        var postcode = jQuery('#billing\\:postcode_input').val();
        var houseNumber = jQuery('#billing\\:postcode_housenumber').val();
        if (postcode !=  '' &&  houseNumber  !=  '') {
        geocodeAddress(postcode + ' ' + houseNumber,{},function(location,item) {
            if (location) {
                form.longlat = location;
                jQuery(form).find('[name=latitude]').val(location.lat());
                jQuery(form).find('[name=longitude]').val(location.lng());

                setTimeout(function() {
                    //checkout.update({'payment-method': 1,'shipping-method': 1});
                },100)
            }
        });
        }
    }
}

jQuery(function() {

    jQuery('#billing\\:postcode').change(function() {
        updateLongLatForField(this);
    });
    jQuery('#billing\\:postcode_housenumber').change(function() {
        updateLongLatForField(this);
    });
    updateLongLatForField(jQuery('#billing\\:postcode')[0]);

    //shippingMethod.save = function() {
    //    ShippingMethod.prototype.save.call(this);
    //    checkout.reloadProgressBlock('shipping');
    //}
});


function get_save_billing_function(url, set_methods_url, update_payments, triggered)
{
    if(typeof update_payments == 'undefined')    {
        var update_payments = false;
    }

    if(typeof triggered == 'undefined')    {
        var triggered = true;
    }

    if(!triggered){
        return function(){return;};
    }

    return function(itemId)    {
        var form = $('onestepcheckout-form');
        var items = exclude_unchecked_checkboxes($$('input[name^=billing]').concat($$('select[name^=billing]')));
        var names = items.pluck('name');
        var values = items.pluck('value');
        var parameters = {
            shipping_method: $RF(form, 'shipping_method')
        };


        var street_count = 0;
        for(var x=0; x < names.length; x++)    {
            if(names[x] != 'payment[method]')    {

                var current_name = names[x];

                if(names[x] == 'billing[street][]')    {
                    current_name = 'billing[street][' + street_count + ']';
                    street_count = street_count + 1;
                }

                parameters[current_name] = values[x];
            }
        }

        var use_for_shipping = $('billing:use_for_shipping_yes');




        if(use_for_shipping && use_for_shipping.getValue() != '1')    {
            var items = $$('input[name^=shipping]').concat($$('select[name^=shipping]'));
            var shipping_names = items.pluck('name');
            var shipping_values = items.pluck('value');
            var shipping_parameters = {};
            var street_count = 0;

            for(var x=0; x < shipping_names.length; x++)    {
                if(shipping_names[x] != 'shipping_method')    {
                    var current_name = shipping_names[x];
                    if(shipping_names[x] == 'shipping[street][]')    {
                        current_name = 'shipping[street][' + street_count + ']';
                        street_count = street_count + 1;
                    }

                    parameters[current_name] = shipping_values[x];
                }
            }
        }

        var shipment_methods = $$('div.onestepcheckout-shipping-method-block')[0];
        var shipment_methods_found = false;

        if(typeof shipment_methods != 'undefined') {
            shipment_methods_found = true;
        }

        if(shipment_methods_found)  {
            shipment_methods.update('<div class="loading-ajax">&nbsp;</div>');
        }

        var payment_method = $RF(form, 'payment[method]');
        parameters['payment_method'] = payment_method;
        parameters['payment[method]'] = payment_method;

        if(update_payments){
            var payment_methods = $$('div.payment-methods')[0];
            payment_methods.update('<div class="loading-ajax">&nbsp;</div>');
        }

        var totals = get_totals_element();
        totals.update('<div class="loading-ajax">&nbsp;</div>');


        new Ajax.Request(url, {
            method: 'post',
            onSuccess: function(transport)    {
                if(transport.status == 200)    {

                    var data = transport.responseText.evalJSON();

                    // Update shipment methods
                    if(shipment_methods_found)  {
                        shipment_methods.update(data.shipping_method);
                    }

                    if(update_payments){
                        payment_methods.replace(data.payment_method);
                    }

                    totals.update(data.summary);


                }
            },
            onComplete: function(transport){
                if(transport.status == 200)    {
                    if(shipment_methods_found)  {
                        $$('dl.shipment-methods input').invoke('observe', 'click', get_separate_save_methods_function(set_methods_url, update_payments));
                        $$('dl.shipment-methods input').invoke('observe', 'click', function() {
                            $$('div.onestepcheckout-shipment-method-error').each(function(item) {
                                new Effect.Fade(item);
                            });
                        });
                    }

                    if(update_payments){
                        $$('div.payment-methods input[name="payment\[method\]"]').invoke('observe', 'click', get_separate_save_methods_function(set_methods_url));

                        $$('div.payment-methods input[name="payment\[method\]"]').invoke('observe', 'click', function() {
                            $$('div.onestepcheckout-payment-method-error').each(function(item) {
                                new Effect.Fade(item);
                            });
                        });

                        if($RF(form, 'payment[method]') != null)    {
                            try    {
                                var payment_method = $RF(form, 'payment[method]');
                                $('container_payment_method_' + payment_method).show();
                                $('payment_form_' + payment_method).show();
                            } catch(err)    {

                            }
                        }
                    }


                    jQuery('#s_method_servicepointdhl_' + itemId).attr('checked','checked');
                    jQuery('.shipment-methods a[href="#tab-1"]').trigger('click');
                }
            },
            parameters: parameters
        });

    }
}
