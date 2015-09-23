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
                var update = function() {
                    var params = Form.serialize(this.form);
                    params += '&servicepointdhl_postcode=' + item.Postcode;
                    params += '&servicepointdhl_city='  + item.City;
                    var request = new Ajax.Request(
                        this.saveUrl,
                        {
                            method: 'post',
                            onComplete: this.onComplete,
                            onSuccess: function (transport){
                                if (transport && transport.responseText){
                                    try{
                                        response = eval('(' + transport.responseText + ')');
                                    }
                                    catch (e) {
                                        response = {};
                                    }
                                }
                                $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
                                // s_method_servicepointdhl_NL-101302
                                jQuery('#s_method_servicepointdhl_' + item.Id).attr('checked','checked');
                            },
                            onFailure: checkout.ajaxFailure.bind(checkout),
                            parameters: params
                        }
                    );
                }
                update.call(billing);
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
        var postcode = jQuery('#billing\\:postcode').val();
        var houseNumber = jQuery('#billing\\:postcode_housenumber').val();
        geocodeAddress(postcode + ' ' + houseNumber ,{},function(location,item) {
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

jQuery(function() {

    jQuery('#billing\\:postcode').change(function() {
        updateLongLatForField(this);
    });
    jQuery('#billing\\:postcode_housenumber').change(function() {
        updateLongLatForField(this);
    });
    updateLongLatForField(jQuery('#billing\\:postcode')[0]);

    shippingMethod.save = function() {
        ShippingMethod.prototype.save.call(this);
        checkout.reloadProgressBlock('shipping');
    }
});