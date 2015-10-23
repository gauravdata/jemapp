/**
 * Specific functionality for One Step Checkout
 */
billink.subModules.push(new function()
{
    var self = this;

    self.billinkAgreementId = '';
    self.formController = null;
    self.validationError = '';

    //Callback from pageController
    self.onPageLoad = function()
    {
        hideBillinkAgreements(billink.agreementId);
    };

    //Callback from formController
    self.onFormLoad = function()
    {
        self.billinkAgreementId = billink.agreementId;
        self.formController = billink.paymentFormController;
        self.validationError = billink.validationError;

        //Update field visiblities on loading
        if(!billink.isAlternateDeliveryAddressAllowed)
        {
            addDifferentAddressMessage();
        }
        else
        {
            setDeliveryAddressFieldVisiblity();
        }

        //Hide billink form when the option is disabled
        checkBillinkDisabled();

        //Update agreements on load
        updateBillinkAgreements(self.billinkAgreementId);

        //Show the validationErrors
        setValidationErrorMessage();

        //Update the address fields
        updateBillingAddressFromOscAddress();
        updateShippingAddressFromOscAddress();


        //Attach event to billink payment field
        $$('div.payment-methods input[name="payment\[method\]"]').each(function(option){
            option.observe('change', function() {
                updateBillinkAgreements(self.billinkAgreementId);
            });
        });

        //Attach event handler on billing address fields
        $$('#billing_address_list .onestepcheckout-address-line').each(function(field) {
           field.observe('change', function(){
               updateBillingAddressFromOscAddress();
           });
        });
        
        //The postcode API module refreshes the billink form when the postcode changes. But a streetnumber
        //does not trigger a form update. Therefore we have to observer this manually
        var billingPostcodeHousenumberField = $('billing:postcode_housenumber');
        if(billingPostcodeHousenumberField)
        {
            billingPostcodeHousenumberField.observe('change', function() {
                $('billink_housenumber_input').value = billingPostcodeHousenumberField.value;
            });
        }

        //Postcode API event for shipping streetnumber (note that billink only includes shipping fields when this is
        //set in the config)
        var shippingPostcodeHousenumberField = $('shipping:postcode_housenumber');
        var billinkShippingPostcodeHousenumberField = $('billink_delivery_housenumber_input');
        if(shippingPostcodeHousenumberField && billinkShippingPostcodeHousenumberField)
        {
            shippingPostcodeHousenumberField.observe('change', function() {
                billinkShippingPostcodeHousenumberField.value = shippingPostcodeHousenumberField.value;
            });
        }

        //The postcode API module has a field for the housenumber extension. This field is created and destroyed when
        //necessary. Because we cannot attach observers to a field that does not exists, we need to do things differently
        document.observe('change', function(e){
            if(e.srcElement.id == "billing:postcode_housenumber_addition")
            {
                if($('billing:postcode_housenumber_addition').value == '__none__')
                {
                    $('billink_housenumber_extension_input').value = '';
                }
                else {
                    $('billink_housenumber_extension_input').value = $('billing:postcode_housenumber_addition').value;
                }
            }
            if(e.srcElement.id == "shipping:postcode_housenumber_addition")
            {
                var billinkShippingHousenumberExtensionInput = $('billink_delivery_housenumber_extension_input');
                if(billinkShippingHousenumberExtensionInput)
                {
                    billinkShippingHousenumberExtensionInput.value = $('shipping:postcode_housenumber_addition').value;
                }
            }
        });

        //Attach event listener to country field.
        //When this changes to a country for which the plugin is not enabled, the payment form is not loaded and this
        //init method is not called. Therefore the Billink agreements are not set to hidden (but it should be, because
        //billink is not a valid option anymore)
        $('billing:country_id').observe('change', function(){
            hideBillinkAgreements(self.billinkAgreementId);
        });

        //Attach event handler on shipping address fields
        $$('#shipping_address_list .onestepcheckout-address-line').each(function(field) {
            field.observe('change', function(){
                updateShippingAddressFromOscAddress();
            });
        });

        //Attach event listener to phone number field
        //The phone field is not always visible (can be configured per checkout)
        var defaultPhoneField = $('billing:telephone');
        if(defaultPhoneField)
        {
            defaultPhoneField.observe('change', function () {
                updatePhoneNumber();
            });
        }

        //Event listeners for birthday
        $$('.input-box.customer-dob input').each(function(field) {
            field.observe('change', function(){
                updateBirthDateFields();
            });
        });
    };

    /**
     * OSC does not hide the form when the options is disabled. So we'll do it ourselves
     */
    var checkBillinkDisabled = function()
    {
        var billinkPaymentRadioOption = $('p_method_billink');

        //If the option is disabled and checked, than select another option
        if(billinkPaymentRadioOption.disabled && billinkPaymentRadioOption.checked)
        {
            billinkPaymentRadioOption.checked = false;
            $('container_payment_method_billink').setStyle({display: 'none'});
        }
    };

    /**
     * Use the OSC address lines as input for the address resolver
     */
    var updateBillingAddressFromOscAddress = function()
    {
        var billingAddressLine = '';
        $$('#billing_address_list .onestepcheckout-address-line').each(function(field){
            billingAddressLine += field.value;
        });

        self.formController.updateBillingAddressFields(billingAddressLine);
    };

    /**
     * Use the OSC address lines as input for the address resolver
     */
    var updateShippingAddressFromOscAddress = function()
    {
        var shippingAddressLine = '';
        $$('#shipping_address_list .onestepcheckout-address-line').each(function(field){
            shippingAddressLine += field.value;
        });

        self.formController.updateShippingAddressFields(shippingAddressLine);
    };

    /**
     * Show validation message
     * Osc does not show the validation message from the payment exception. (it just displays: 'vul onderstaande gegevens in')
     * therefore, we overwrite this error with the real error message
     */
    var setValidationErrorMessage = function()
    {
        var errorNotification = $$('.onestepcheckout-error.onestepcheckout-payment-method-error');
        var billinkPaymentRadioOption = $('p_method_billink');
        var hasValidationErrorMessage = self.validationError != '';

        if(billinkPaymentRadioOption.checked && hasValidationErrorMessage && errorNotification.first())
        {
            errorNotification.first().update(self.validationError);
        }
    };

    /**
     * Add a message letting the user know that Billink cannot be used with two different addresses
     */
    var addDifferentAddressMessage = function()
    {
        var checkbox = $('billing:use_for_shipping_yes');

        //Remove the old message
        var oldMessage = $(billink.checkoutPageController.differentAddressMessageElementId);
        if(oldMessage)
        {
            oldMessage.remove();
        }
        
        //Add message
        if(checkbox && !checkbox.checked && billink.isReadyToUse)
        {
            var li = checkbox.up(1);
            $(li).insert({
                after: billink.checkoutPageController.getDifferentAddressMessage()
            });
        }
    };

    /**
     * Show the billink agreement when Billink payment method is selected
     */
    var updateBillinkAgreements = function(billinkAgreementId)
    {
        var billinkPaymentRadioOption = $('p_method_billink');
        var billinkAgeementElement = $('agreement-' + billinkAgreementId);

        if(billinkAgeementElement)
        {
            var billinkAgreementLiElement = billinkAgeementElement.up('li');

            if (billinkPaymentRadioOption && billinkPaymentRadioOption.checked)
            {
                billinkAgreementLiElement.setStyle({display: 'block'});
            }
            else
            {
                billinkAgreementLiElement.setStyle({display: 'none'});
            }
        }
    };

    /**
     * Hide the billink agreements
     * @param billinkAgreementId
     */
    var hideBillinkAgreements = function(billinkAgreementId)
    {
        //Do not use self.billinkAgreementId here, this function can be called before the init is called. (should be refactored)

        var billinkAgeementElement = $('agreement-' + billinkAgreementId);
        if(billinkAgeementElement)
        {
            var billinkAgreementLiElement = billinkAgeementElement.up('li');
            billinkAgreementLiElement.setStyle({display: 'none'});
        }
    };

    /**
     * Hide the delivery address fields when the user has enabled 'use-for-shipping' field
     */
    var setDeliveryAddressFieldVisiblity = function()
    {
        var checkbox = $('billing:use_for_shipping_yes');
        if(checkbox && checkbox.checked && billink.isReadyToUse)
        {
            $('billink-delivery-address-fields').hide();
        }
    };

    /**
     * Update the billink phonenumber field with the Osc address phonenumber field
     */
    var updatePhoneNumber = function()
    {
        var billinkPhoneNumberField = $('billink_phone_input');
        if(billinkPhoneNumberField)
        {
            billinkPhoneNumberField.value = $('billing:telephone').value;
        }
    };

    /**
     * Update the billink birthdate fields from the checkout birthdate fields
     */
    var updateBirthDateFields = function()
    {
        $('billink_dob:year').value = $('billing:year').value;
        $('billink_dob:month').value = $('billing:month').value;
        $('billink_dob:day').value = $('billing:day').value;
        billink.paymentFormSession.savePaymentFormFieldsInSession();
    }
});