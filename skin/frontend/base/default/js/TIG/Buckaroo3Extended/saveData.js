jQuery( document ).on('change', '.buckaroo3extended_input input, .buckaroo3extended_input select', function() {
//jQuery('.buckaroo3extended_input').find('input,select').live('change', function() {
    sendData(jQuery(this));
});

jQuery( document ).on('change', '#buckaroo3extended_directdebit_account_owner, #buckaroo3extended_directdebit_account_number', function() {
//jQuery('#buckaroo3extended_directdebit_account_owner, #buckaroo3extended_directdebit_account_number').live('change', function() {
    sendData(jQuery(this));
});