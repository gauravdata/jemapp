jQuery('.buckaroo3extended_input').find('input,select').bind('change', function() {
        sendData(jQuery(this));
    }
);

jQuery('#buckaroo3extended_directdebit_account_owner, #buckaroo3extended_directdebit_account_number').bind('change', function() {
        sendData(jQuery(this));
    }
);