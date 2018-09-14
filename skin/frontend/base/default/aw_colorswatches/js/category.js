var AWColorswatchCategoryProductAdder = Class.create();
AWColorswatchCategoryProductAdder.prototype = {
    initialize: function(config) {
        var me = this;
        me.addToCartUrl  = config.addToCartUrl;
        me.productId = config.productId;
        var initializeFn = function() {
            me.containerEl = $$(config.containerElSelector).first();
            var insertAfterEl = (config.insertContainerAfterElFn || Prototype.emptyFunction)(me.containerEl);
            if (insertAfterEl) {
                insertAfterEl.insert({'after': me.containerEl});
            }
            me.addToCartBtn = config.addToCartBtnSelectFn(me.containerEl);
            me.init();
        };
        if (document.loaded) {
            initializeFn();
        } else {
            document.observe('dom:loaded', initializeFn);
        }
    },

    init: function() {
        var me = this;
        me.addToCartBtn.setAttribute('_onclick', me.addToCartBtn.getAttribute('onclick'));
        me.addToCartBtn.removeAttribute('onclick');
        me.addToCartBtn.observe('click', function(e){
            Event.stop(e);
            e.stopImmediatePropagation();
            me.onAddToCartClick();
        });
    },

    onAddToCartClick: function() {
        if (!this.validate()) {
            return;
        }
        var url = this._getAddToCartUrl();
        setLocation(url);
    },

    validate: function() {
        var validator = new Validation(this.containerEl);
        return validator.validate();
    },

    _getAddToCartUrl: function() {
        var data = [];
        this.containerEl.select('.super-attribute-select').each(function(el){
            data.push(
                encodeURIComponent(el.getAttribute('name')) + "=" + encodeURIComponent(el.getValue())
            );
        });
        var url = this.addToCartUrl;
        if (data.length) {
            url += "?" + data.join("&");
        }
        return url;
    }
};

/** REWRITE BASE FUNCTIONALITY */
Product.Config.prototype.reloadPrice = function(){
    if (this.config.disablePriceReload || optionsPrice[this.config.productId] == undefined) {
        return;
    }
    var price    = 0;
    var oldPrice = 0;
    for(var i=this.settings.length-1;i>=0;i--){
        var selected = this.settings[i].options[this.settings[i].selectedIndex];
        if(selected.config){
            price    += parseFloat(selected.config.price);
            oldPrice += parseFloat(selected.config.oldPrice);
        }
    }

    optionsPrice[this.config.productId].changePrice('config', {'price': price, 'oldPrice': oldPrice});
    optionsPrice[this.config.productId].reload();
    return price;

    if($('product-price-'+this.config.productId)){
        $('product-price-'+this.config.productId).innerHTML = price;
    }
    this.reloadOldPrice();
}
var optionsPrice = {};