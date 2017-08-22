Validation.add('validate-for-discount', 'Please enter a correct data.', function (regSearch) {
    return /^(-?\d+%?)?$/.test(regSearch);
});
