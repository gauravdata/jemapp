

function productsUpdate()
{
//    $('#myForm').submit(function() {
        // get all the inputs into an array.
        var $inputs = $j('#editData :input');

        // not sure if you wanted this, but I thought I'd add it.
        // get an associative array of just the values.
        var values = {};
        $inputs.each(function() {
            values[this.name] = $j(this).val();
        });
        console.log(values);
//alert('test');
//return null;
//    });
/*
editData.action = this.massUpdateProducts;
    console.log(editData.action);
    editData.submit();*/
}
