/**
 * Controller for the billink payment form
 */
billink.paymentFormController = new function()
{
    var self = this;
    self.addressParserUrl = '';
    self.billinkFieldSaver = null;

    /**
     * This function is called when the Billink form is loaded
     */
    self.init = function(billinkFieldSaver, addressParserUrl, birthDateYear, birthDateMonth, birthDateDay)
    {
        self.billinkFieldSaver = billinkFieldSaver;
        self.addressParserUrl = addressParserUrl;

        //Update field visiblities on loading
        self.updateFieldVisibilies();

        //Update day fields
        self.updateBirthDateFields();

        //Set default birthdate
        self.setDefaultBirthDate(birthDateYear, birthDateMonth, birthDateDay);

        //Attach listener
        $$('.billink-customer-type').each(function(radioButton){
            radioButton.observe('change', function() {
                self.updateFieldVisibilies();
            });
        });

        $$('#billink-birthdate-fields select').each(function(select){
            select.observe('change', function() {
                self.updateBirthDateFields();
            });
        });

        //Send form loaded event to submodules
        for (var i = 0; i < billink.subModules.length; i++)
        {
            var subMod = billink.subModules[i];
            if (typeof subMod.onFormLoad === "function") {
                subMod.onFormLoad();
            }
        }
    };

    /**
     * Retrieve the parsed address from the server
     * @param addressLine optional parameter
     */
    self.updateBillingAddressFields = function(addressLine)
    {
        var url = self.getAddressParserUrl('billing', addressLine);
        new Ajax.Request(url , {
            method: 'post',
            onSuccess: function(transport){
                var json = transport.responseText.evalJSON();
                if(json.streetName != null && json.streetName != '') {
                    $('billink_street_input').value = json.streetName;
                    $('billink_housenumber_input').value = json.houseNumber;
                    $('billink_housenumber_extension_input').value = json.houseNumberExtension;

                    //Save data in session
                    self.billinkFieldSaver.savePaymentFormFieldsInSession();
                }
            }
        });
    };

    /**
     * Update shipping address fields
     * @param addressLine
     */
    self.updateShippingAddressFields = function(addressLine)
    {
        var url = self.getAddressParserUrl('shipping', addressLine);
        new Ajax.Request(url , {
            method: 'post',
            onSuccess: function(transport){
                var json = transport.responseText.evalJSON();
                if(json.streetName != null && json.streetName != '') {
                    $('billink_delivery_street_input').value = json.streetName;
                    $('billink_delivery_housenumber_input').value = json.houseNumber;
                    $('billink_delivery_housenumber_extension_input').value = json.houseNumberExtension;

                    //Save data in session
                    self.billinkFieldSaver.savePaymentFormFieldsInSession();
                }
            }
        });
    };

    /**
     * @param type
     * @param addressLine
     * @returns {string}
     */
    self.getAddressParserUrl = function(type, addressLine)
    {
        var url = self.addressParserUrl  + '?type='+type;
        if(addressLine && addressLine != "")
        {
            url = url + '&address=' + addressLine;
        }
        return url;
    };

    /**
     * Update field visibilities when the customer type is changed
     */
    self.updateFieldVisibilies = function()
    {
	if ($('billink_checkout_type_company')) {
	        var isCompany = $('billink_checkout_type_company').checked;
	} else {
		isCompany = false;
	}
        if (isCompany)
        {
            $('billink-chamberofcommerce-field').setStyle({display: 'block'});
            $('billink-birthdate-fields').setStyle({display: 'none'});
            $('billink-sex-radio').setStyle({display: 'none'});
        }
        else
        {
	    if ($('billink-chamberofcommerce-field')) {
	            $('billink-chamberofcommerce-field').setStyle({display: 'none'});
	    }
	    if ($('billink-birthdate-fields')) {
	            $('billink-birthdate-fields').setStyle({display: 'block'});
	    }
	    if ($('billink-sex-radio')) {
	            $('billink-sex-radio').setStyle({display: 'block'});
	    }
        }
    };

    /**
     * Validate and update the birthdate fields
     */
    self.updateBirthDateFields = function()
    {
        var month = $('billink_dob:month').value;
        var year = $('billink_dob:year').value;
        var daysInMonth = new Date(year, month, 0).getDate();
        var daySelect = $('billink_dob:day');
        var currentValue = daySelect.value ? daySelect.value  : 1;

        //Add days that are not yet present
        for(var i = 1; i <= daysInMonth; i++)
        {
            var option = $('billink_dob:day_'+i);
            if(!option) {
                daySelect.insert('<option id="billink_dob:day_' + i + '" value="' + i + '">' + i + '</option>');
            }
        }

        //Remove days that are not in this month
        for(var j = daysInMonth+1; j <= 31; j++)
        {
            var option = $('billink_dob:day_'+j);
            if(option) {
                option.remove();
            }
        }

        daySelect.value = currentValue;
    };

    /**
     * Set the default value for the birth date
     * @param year
     * @param month
     * @param day
     */
    self.setDefaultBirthDate = function(year, month, day)
    {
        if(year && month && day)
        {
            $('billink_dob:year').value = year;
            $('billink_dob:month').value = month;
            $('billink_dob:day').value = day;
        }
    };
};
