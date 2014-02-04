(function ( $ ) {
    function setHeight(list, height){
        jQuery(list).each(function(i, v){
            jQuery(v).height(height);
        });
    }

    $.fn.syncheight = function() {
        var elements = [];
        var prevY = -1;
        var h = 0;
        this.each(function(index, value){
            var jValue = jQuery(value);
            jValue.css('height', 'auto');
            var top = jValue.offset().top;
            if(prevY != -1 && prevY != top){
                setHeight(elements, h);
                elements = [];
                top = -1;
                h = 0;
            }
            elements.push(value);
            if(jValue.css('-moz-box-sizing') == 'border-box'){
                h = jValue.outerHeight() > h ? jValue.outerHeight() : h;
            }else{
                h = jValue.height() > h ? jValue.height() : h;
            }

            prevY = top;
        });
        setHeight(elements, h);
        return this;
    }
}( jQuery ));