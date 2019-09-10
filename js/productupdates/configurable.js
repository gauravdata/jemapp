PunConfigConfigurable = Class.create();
PunConfigConfigurable.prototype = {
    initialize: function(config) {
        this.config     = config;
        this.taxConfig  = this.config.taxConfig;
        
        this.stockStatus = {};
        this.productIds = {};
        this.selectedIndex = null;
       
        this.settings   = $$('.pun-super-attribute-select');
        
        this.state      = new Hash();
        this.priceTemplate = new Template(this.config.template);
        this.prices     = config.prices;
       
        this.settings.each(function(element){
            Event.observe(element, 'change', this.configure.bind(this))
        }.bind(this));

        // fill state
        this.settings.each(function(element){
            var attributeId = element.id.replace(/[a-z]*/, '');
            if(attributeId && this.config.attributes[attributeId]) {
                element.config = this.config.attributes[attributeId];
                element.attributeId = attributeId;
                this.state[attributeId] = false;
            }
        }.bind(this));

        // Init settings dropdown
        var childSettings = [];
        for(var i=this.settings.length-1;i>=0;i--) {
            var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
            var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
            if (i == 0){
                this.fillSelect(this.settings[i])
            } else {
                this.settings[i].disabled = true;
            }
            $(this.settings[i]).childSettings = childSettings.clone();
            $(this.settings[i]).prevSetting   = prevSetting;
            $(this.settings[i]).nextSetting   = nextSetting;
            childSettings.push(this.settings[i]);
        }

        // Set values to inputs
        this.configureForValues();
        document.observe("dom:loaded", this.configureForValues.bind(this));
    },
    
    configureForValues: function () {
        if (this.values) {
            this.settings.each(function(element){
                var attributeId = element.attributeId;
                element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];
                this.configureElement(element);
            }.bind(this));
        }
    },

    configure: function(event) {
     
        var element = Event.element(event);            
        this.configureElement(element);         
        if(!element.nextSetting) {          
             $('aw-pun-configurable-stock').update(this.config.linkMessage[this.stockStatus[element.value]]);         
        }       
    },
    
    getSelectedIndex: function(id) {
      var index = "";
      for(i=0; i<this.settings.length; i++) {
           if(this.settings[i].selectedIndex) { 
               index += this.settings[i].config.options[this.settings[i].selectedIndex - 1].id;
           }
           if(this.settings[i].config.id == id) {
               break;
           }           
      }
      return index;        
    },
   
    configureElement : function(element) {
        
        this.reloadOptionLabels(element);
      
        if(element.value) {
            this.state[element.config.id] = element.value;
            if(element.nextSetting) {
                element.nextSetting.disabled = false;
                this.fillSelect(element.nextSetting);
                this.resetChildren(element.nextSetting);
            }
        }
        else {
            this.resetChildren(element);
        }
        this.reloadPrice();
    },

    reloadOptionLabels: function(element){
        var selectedPrice;
        if(element.options[element.selectedIndex].config && !this.config.stablePrices){
            selectedPrice = parseFloat(element.options[element.selectedIndex].config.price)
        }
        else{
            selectedPrice = 0;
        }
        for(var i=0;i<element.options.length;i++){
            if(element.options[i].config) {
                
                var stockIndex = null;
                if(this.selectedIndex) {                    
                    stockIndex = this.config.stockMessage[this.stockStatus[element.options[i].config.id]];
                }                
                element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice);                
                if(stockIndex != undefined && stockIndex != null) {                       
                    element.options[i].text += ' - ' + stockIndex;
                } 
                
                
            }
        }
    },

    resetChildren : function(element){
        if(element.childSettings) {
            for(var i=0;i<element.childSettings.length;i++){
                element.childSettings[i].selectedIndex = 0;
                element.childSettings[i].disabled = true;
                if(element.config){
                    this.state[element.config.id] = false;
                }
            }
        }
    },
    
    cacheConfigStock: function(element, selectedIndex) {
        this.selectedIndex = selectedIndex;
        for(i=0; i<element.config.options.length; i++) {
            option = element.config.options[i];                
            for(product in this.config.valueByProduct.products) {                    
                var unique = selectedIndex + '' + option.id;                    
                if(this.config.valueByProduct.products[product] == unique) {
                    this.productIds[product] = option.id;                        
                    for(stock in this.config.valueByProduct.stocks) {                            
                        if(stock == product) {
                            this.stockStatus[option.id] = this.config.valueByProduct.stocks[stock];
                        }                            
                    }                      
                }                  
            }                
        }         
    },


    fillSelect: function(element) {
        this.selectedIndex = null;
        this.stockStatus = {};
        this.productIds = {};               
        if(this.settings.length == 1) {            
            this.cacheConfigStock(element, this.getSelectedIndex(element.config.id));
        }
        if(!element.nextSetting && element.prevSetting) {  
            this.cacheConfigStock(element, this.getSelectedIndex(element.prevSetting.config.id));            
        }
        
       
        var attributeId = element.id.replace(/[a-z]*/, '');
        var options = this.getAttributeOptions(attributeId);
        this.clearSelect(element);
        element.options[0] = new Option(this.config.chooseText, '');

        var prevConfig = false;
        if(element.prevSetting){
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }
       
        if(options) {
            var index = 1;
            for(var i=0;i<options.length;i++){
                var allowedProducts = [];
                if(prevConfig) {
                    for(var j=0;j<options[i].products.length;j++){
                        if(prevConfig.config.allowedProducts
                            && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                } else {
                    allowedProducts = options[i].products.clone();
                }

                if(allowedProducts.size()>0) {                    
                  
                    options[i].allowedProducts = allowedProducts;
                    
                     
                    var optionLabel = this.getOptionLabel(options[i], options[i].price);
                    if(this.selectedIndex) {
                        stockIndex = this.config.stockMessage[this.stockStatus[options[i].id]];
                        optionLabel += ' - ' + stockIndex;
                    }                    
                    element.options[index] = new Option(optionLabel, options[i].id);
                    
                     
                    if (typeof options[i].price != 'undefined') {
                        element.options[index].setAttribute('price', options[i].price);
                    }
                    element.options[index].config = options[i];
                    index++;
                }
            }
        }
    },

    getOptionLabel: function(option, price) {
        return option.label;
        
        price = parseFloat(price);
        var str = option.label;
        if(price){
            if (this.taxConfig.showBothPrices) {
                str+= ' ' + this.formatPrice(excl, true) + ' (' + this.formatPrice(price, true) + ' ' + this.taxConfig.inclTaxTitle + ')';
            } else {
                str+= ' ' + this.formatPrice(price, true);
            }
        }
        return str;
    },

    formatPrice: function(price, showSign){
        var str = '';
        price = parseFloat(price);
        if(showSign){
            if(price<0){
                str+= '-';
                price = -price;
            }
            else{
                str+= '+';
            }
        }

        var roundedPrice = (Math.round(price*100)/100).toString();

        if (this.prices && this.prices[roundedPrice]) {
            str+= this.prices[roundedPrice];
        }
        else {
            str+= this.priceTemplate.evaluate({price:price.toFixed(2)});
        }
        return str;
    },

    clearSelect: function(element){
        for(var i=element.options.length-1;i>=0;i--){
            element.remove(i);
        }
    },

    getAttributeOptions: function(attributeId){
        if(this.config.attributes[attributeId]){
            return this.config.attributes[attributeId].options;
        }
        return null;
    },

    reloadPrice: function() {
       
        var price    = 0;
        var oldPrice = 0;
        for(var i=this.settings.length-1;i>=0;i--) {
            
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
             
            if(selected.config) {                
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }
      
        return price;
 
    },

    reloadOldPrice: function(){
        if (this.config.disablePriceReload) {
            return;
        }
        if ($('old-price-'+this.config.productId)) {

            var price = parseFloat(this.config.oldPrice);
            for(var i=this.settings.length-1;i>=0;i--){
                var selected = this.settings[i].options[this.settings[i].selectedIndex];
                if(selected.config){
                    price+= parseFloat(selected.config.price);
                }
            }
            if (price < 0)
                price = 0;
            price = this.formatPrice(price);

            if($('old-price-'+this.config.productId)){
                $('old-price-'+this.config.productId).innerHTML = price;
            }

        }
    }
};
