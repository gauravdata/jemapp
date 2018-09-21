var AWColorswatchManager = Class.create();
AWColorswatchManager.prototype = {
    _updateQueue: {},
    initialize: function(config) {
        this.url = config.url;
        this.selectorFnList = config.selectorFnList;
        this.productInfo = {};
        this.data = {};
    },

    addItem: function(obj) {
        var me = this;
        var productId = obj.config.productId;
        if (Object.isUndefined(this.data[productId])) {
            this.data[productId] = {};
            this.productInfo[productId] = {};
            Event.observe(document, 'dom:loaded', function(e){
                Object.keys(me.selectorFnList).each(function(key){
                    me.productInfo[productId][key] = me.selectorFnList[key](obj.sourceEl).innerHTML;
                });
            });
        }
        obj.config.sortOrder = Object.values(this.data[productId]).length;
        this.data[productId][obj.config.attributeId] = obj;
    },

    removeItem: function(obj) {
        var productId = obj.config.productId;
        var attributeId = obj.config.attributeId;
        if (Object.isUndefined(this.data[productId]) || Object.isUndefined(this.data[productId][attributeId])) {
            return;
        }
        delete this.data[productId][attributeId];
    },

    getSortedProductAttributes: function(productId, isResultAsKeyList) {
        var items = Object.values(this.data[productId]);
        items = items.sort(function(a, b){
            return a.config.sortOrder - b.config.sortOrder;
        });
        if (isResultAsKeyList) {
            var result = [];
            items.each(function(obj){
                result.push(obj.config.attributeId);
            });
            return result;
        }
        return items;
    },

    triggerUpdate: function(productId, attributeId, optionId) {
        //hide/show options according products information and current selection
        this._recollectOptionAvailability(productId, attributeId, optionId);

        //change native selector value for price update
        var selectedValueList = {};
        this.getSortedProductAttributes(productId).each(function(obj){
            selectedValueList[obj.config.attributeId] = obj.getSelectedValue();
        });
        this.getSortedProductAttributes(productId).each(function(obj){
            if (null === selectedValueList[obj.config.attributeId]) {
                return;
            }
            obj.setValueInSourceEl(selectedValueList[obj.config.attributeId]);
        });

        this.productInfoUpdate(productId);
    },

    productInfoUpdate: function(productId) {
        var me = this;
        var chooseMap = {};
        this.getSortedProductAttributes(productId, true).each(function(attributeId){
            var obj = me.data[productId][attributeId];
            if (null === obj.getSelectedValue()) {
                chooseMap[attributeId] = null;
            } else {
                chooseMap[attributeId] = obj.config.optionData[obj.getSelectedValue()].products;
            }
        });
        if (Object.values(chooseMap).indexOf(null) !== -1) {
            //not all attribute selected
            return;
        }
        var childProductIdList = null;
        Object.values(chooseMap).each(function(productList){
            childProductIdList = childProductIdList||productList;
            childProductIdList = childProductIdList.intersect(productList);
        });
        var childProductId = childProductIdList.first();
        this._updateHTMLTo(childProductId, productId);
    },

    _recollectOptionAvailability: function(productId, attributeId, optionId) {
        var lastAttribute = this.data[productId][attributeId];
        var productIdsByLastSelection = lastAttribute.config.optionData[optionId].products;

        var availabilityData = {};
        Object.values(this.data[productId]).each(function(obj){
            var selectedOptionId = obj.getSelectedValue();
            if (null === selectedOptionId) {
                return;
            }
            var products = obj.config.optionData[selectedOptionId].products;
            if (productIdsByLastSelection.intersect(products).length === 0){
                //unselect options when not match current selection
                obj.unselect();
            } else {
                //collect available productIDs by current selection
                availabilityData[obj.config.attributeId] = products;
            }
        });

        //hide|show options when not match current selection
        var me = this;
        var needToRecollect = false;
        Object.values(this.data[productId]).each(function(obj){
            var availability = null;//collect availability list for current attribute
            Object.keys(availabilityData).each(function(attrId){
                var currentObj = me.data[productId][attrId];
                if (obj.config.sortOrder <= currentObj.config.sortOrder) {//it make a vertical dependence
                    return;
                }
                if (null === availability) {
                    availability = availabilityData[attrId];
                }
                availability = availability.intersect(availabilityData[attrId]);
            });
            if (!obj.config.isUseSwatch) {
                return;
            }
            obj.items.each(function(item){
                var optionId = item.getAttribute('option-id');
                var products = obj.config.optionData[optionId].products;
                var currentAvailability = (availability||products).intersect(products);
                if (currentAvailability.length > 0) {
                    item.show();
                    obj.checkIsSaleable(item, currentAvailability) || obj.unselect(item);
                } else {
                    item.hide();
                }
            });
        });
    },

    _updateHTMLTo: function(productId, baseProductId) {
        var me = this;
        var _updateFn = function(infoToUpdate){
            var firstAttribute = Object.values(me.data[baseProductId]).first();
            Object.keys(infoToUpdate).each(function(key){
                var html = infoToUpdate[key];
                var targetEl = me.selectorFnList[key](firstAttribute.sourceEl);
                try {
                    targetEl.update(html);
                } catch (e) {
                    console.error(e.message);
                    targetEl.innerHTML = html;
                }
            });
            me._patchForZoomers(infoToUpdate, productId);
        };
        this._updateQueue[baseProductId] = productId;
        if (!Object.isUndefined(this.productInfo[productId])) {
            _updateFn(this.productInfo[productId]);
            delete me._updateQueue[baseProductId];
        } else {
            new Ajax.Request(this.url, {
                parameters: {
                    "parts[]": Object.keys(me.selectorFnList),
                    "product_id": productId
                },
                onComplete: function(transport) {
                    var json = transport.responseText.evalJSON();
                    if (!json.success) {
                        console.warn("Cannot get info for product: " + productId);
                        return;
                    }
                    me.productInfo[productId] = json.data;
                    if (me._updateQueue[baseProductId] === productId) {
                        _updateFn(me.productInfo[productId]);
                        delete me._updateQueue[baseProductId];
                    }
                }
            });
        }
    },

    _patchForZoomers: function(infoToUpdate, productId) {
        if (Object.isUndefined(infoToUpdate['media'])) {
            return;
        }
        var content = infoToUpdate['media'];
        var nativeZoomerString = content.match(/new Product.Zoom\([^)]*\)/);
        if (nativeZoomerString) {
            var mediaBox = this.selectorFnList['media'](
                Object.values(this.data[productId]).first()
            );
            mediaBox.select('img').first().observe('load', function(e){
                try {
                    eval(nativeZoomerString[0]);
                } catch (e) {
                    console.warn("Can not start zoomer");
                    console.error(e.message);
                }
            });
        }
    }
};
var AWColorswatch = Class.create();
AWColorswatch.prototype = {
    _tpl: '<div class="aw-colorswatches-attribute">{{items}}</div>',
    _itemTpl: '<div class="aw-colorswatches-attribute-option" option-id="{{option_id}}" title="{{title}}" sort-order="{{sort_order}}"></div>',
    _itemStateCSSList: {
        selected: 'aw-colorswatches-attribute-option__state-selected',
        disabled: 'aw-colorswatches-attribute-option__state-disabled'
    },
    _sourceAdditionalCSSClass: 'aw-colorswatches-attribute-source',
    _labelTextCSSClass: 'aw-colorswatches-attribute-label-text',
    _labelTextFormat: ": %s",

    initialize: function(config){
        this.sourceEl = $$(config.sourceElSelector).first();
        this.labelEl = (config.labelSelectFn || Prototype.emptyFunction)(this.sourceEl);
        this.size  = config.size;

        this.config = {
            productId: config.productId,
            attributeId: config.attributeId,
            isUseSwatch: config.isUseSwatch,
            isShowPopup: config.isShowPopup,
            optionData: config.optionData
        };

        AWColorswatchManagerInstance.addItem(this);
        if (this.config.isUseSwatch) {
            this.init();
        } else {
            this.initNativeSourceEl();
        }
    },

    init: function() {
        this.sourceEl.addClassName(this._sourceAdditionalCSSClass);
        this._createSwatchEl();
        this.sourceEl.insert({'before': this.swatchEl});

        var me = this;
        me.items.each(function(item){
            var optionId = item.getAttribute('option-id');
            item.observe('click', function(e){
                me.onItemClick(item, optionId);
            });
            me.checkIsSaleable(item, me.config.optionData[optionId].products);
            if (me.config.isShowPopup && me.config.optionData[optionId].tooltipImg) {
                var tooltip = new AWColorswatchTooltip({
                    imgSrc: me.config.optionData[optionId].tooltipImg,
                    targetEl: item
                });
                item.observe('mouseover', function(e){
                    tooltip.show();
                });
                item.observe('mouseout', function(e){
                    tooltip.hide();
                    tooltip.remove();
                });
            }
        });
    },

    initNativeSourceEl: function() {
        var me = this;
        this.sourceEl.observe('change', function(e){
            var optionId = parseInt(me.sourceEl.getValue());
            AWColorswatchManagerInstance.triggerUpdate(me.config.productId, me.config.attributeId, optionId);
        });
    },

    onItemClick: function(item, optionId) {
        var me = this;
        if (item.hasClassName(me._itemStateCSSList.disabled)) {
            return;
        }
        me.items.each(function(el){
            el.removeClassName(me._itemStateCSSList.selected);
        });
        item.addClassName(me._itemStateCSSList.selected);
        this._addTextToLabel(this.config.optionData[optionId].title);

        AWColorswatchManagerInstance.triggerUpdate(this.config.productId, this.config.attributeId, optionId);
    },

    getSelectedValue: function() {
        var result = null;
        if (!this.config.isUseSwatch) {
            result = parseInt(this.sourceEl.getValue());
            return isNaN(result)?null:result;
        }
        var me = this;
        me.items.each(function(item){
            if (item.hasClassName(me._itemStateCSSList.selected)) {
                result = item.getAttribute('option-id');
            }
        });
        return result;
    },

    setValueInSourceEl: function(optionId) {
        if (this.sourceEl.select('option[value="' + optionId + '"]').length === 0) {
            return;
        }
        this.sourceEl.setValue(optionId);
        if (!this.config.isUseSwatch) {
            return;
        }
        if ("createEvent" in document) {
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("blur", false, true);
            this.sourceEl.dispatchEvent(evt);
            evt = document.createEvent("HTMLEvents");
            evt.initEvent("change", false, true);
            this.sourceEl.dispatchEvent(evt);
        } else {
            this.sourceEl.fireEvent("onblur");
            this.sourceEl.fireEvent("onchange");
        }
    },

    unselect: function(item){
        var me = this;
        this.items.each(function(_item){
            if (!Object.isUndefined(item) && _item.getAttribute('option-id') != item.getAttribute('option-id')) {
                return;
            }
            if (_item.hasClassName(me._itemStateCSSList.selected)) {
                _item.removeClassName(me._itemStateCSSList.selected);
                me.setValueInSourceEl('');
            }
        });
    },

    checkIsSaleable: function(item, availability) {
        var notSaleableList = this.config.optionData[item.getAttribute('option-id')].not_saleable;
        if (availability.intersect(notSaleableList).length === availability.length) {
            this.notSaleable(item);
            return false;
        } else {
            this.saleable(item);
            return true;
        }
    },

    saleable: function(item) {
        item.removeClassName(this._itemStateCSSList.disabled);
        item.update('');
    },

    notSaleable: function(item) {
        item.addClassName(this._itemStateCSSList.disabled);
        item.update('<div></div>');
    },

    _createSwatchEl: function() {
        var me = this;
        me.items = [];

        Object.keys(me.config.optionData).each(function(optionId){
            var data = me.config.optionData[optionId];
            data.option_id = optionId;
            if (data.products.length <= 0) {
                return;
            }
            me.items.push(me._createItemEl(data));
        });

        /* Sorting of items */
        me.items.sort(function(el1, el2) {
            return  (parseInt(el1.getAttribute('sort-order')) || 0) -
                    (parseInt(el2.getAttribute('sort-order')) || 0);
        });

        me.swatchEl = new Element('div');
        me.swatchEl.update(
            me._tpl.replace(/{{items}}/g, "<span class='aw-colorswatches-attribute-items-anchor'></span>")
        );
        me.swatchEl = this.swatchEl.down();
        var anchorEl = me.swatchEl.select('span.aw-colorswatches-attribute-items-anchor').first();
        me.items.each(function(item){
            anchorEl.insert({'before': item});
        });

        anchorEl.remove();
    },

    _createItemEl: function(data) {
        var el = new Element('div');
        el.update(
            this._itemTpl.replace(/{{option_id}}/g, data.option_id)
                         .replace(/{{title}}/g, data.title)
                         .replace(/{{sort_order}}/g, data.sort_order)
        );
        el = el.down();
        el.setStyle({
            'width': this.size[0] + "px",
            'height': this.size[1] + "px"
        });
        if (data.img) {
            el.setStyle({
                'backgroundImage': 'url("' + data.img +'")'
            });
        }
        return el;
    },

    _addTextToLabel: function(text) {
        if (!this.labelEl) {
            return;
        }
        if (this.labelEl.select('.' + this._labelTextCSSClass).length <= 0) {
            var span = new Element('span');
            span.addClassName(this._labelTextCSSClass);
            this.labelEl.appendChild(span);
        }
        text = this._labelTextFormat.replace('%s', text);
        this.labelEl.select('.' + this._labelTextCSSClass).first().update(text);
    },

    _removeTextFromLabel: function() {
        if (!this.labelEl) {
            return;
        }
        var el = this.labelEl.select('.' + this._labelTextCSSClass).first();
        el && el.remove();
    }
};

var AWColorswatchTooltip = Class.create();
AWColorswatchTooltip.prototype = {
    _tpl: "<div class='aw-colorswatches-tooltip'>" +
            "<div class='aw-colorswatches-tooltip-content'></div>" +
            "<div class='aw-colorswatches-tooltip-arrow'>" +
                "<span class='aw-colorswatches-tooltip-arrow-border'></span>" +
                "<span class='aw-colorswatches-tooltip-arrow-content'></span>" +
            "</div>" +
        "</div>",
    _tooltipContentCSSClass: "aw-colorswatches-tooltip-content",
    _tooltipShowCSSClass: "aw-colorswatches-tooltip__show",
    _tooltipArrowCSSClass: "aw-colorswatches-tooltip-arrow",
    _tooltipArrowBottomOrientationCSSClass: "aw-colorswatches-tooltip-arrow__bottom",

    initialize: function(config) {
        this.item = null;
        this.imgSrc = config.imgSrc;
        this.targetEl = config.targetEl;
    },

    create: function() {
        var el = new Element('div');
        el.update(this._tpl);
        this.item = el.down();

        var img = new Element('img');
        img.setAttribute('src', this.imgSrc);
        var me = this;
        img.observe('load', function(e){
            me._resize();
        });

        var tooltipContent = this.item.select('.' + this._tooltipContentCSSClass).first();
        tooltipContent.appendChild(img);

        $$('body').first().appendChild(this.item);
    },

    remove: function() {
        this.item.remove();
        this.item = null;
    },

    show: function() {
        if (!this.item) {
            this.create();
        }
        this._resize();
        this.item.addClassName(this._tooltipShowCSSClass);
    },

    hide: function() {
        if (!this.item) {
            return;
        }
        this.item.removeClassName(this._tooltipShowCSSClass);
    },

    _resize: function() {
        var offset = this.targetEl.cumulativeOffset();
        var scrollOffset = this.targetEl.cumulativeScrollOffset();
        var left = offset.left + this.targetEl.getWidth()/2 - this.item.getWidth()/2;

        var arrowEl = this.item.select('.' + this._tooltipArrowCSSClass).first();
        var top = offset.top - this.item.getHeight() - 10;
        var placeInTop = (offset.top - scrollOffset.top - 10);
        var docHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        var placeInBottom = docHeight - placeInTop - this.targetEl.getHeight() - 10;

        if (placeInTop < this.item.getHeight() && placeInBottom >= this.item.getHeight()) {
            arrowEl.addClassName(this._tooltipArrowBottomOrientationCSSClass);
            top = offset.top + this.targetEl.getHeight() + 10;
        } else {
            arrowEl.removeClassName(this._tooltipArrowBottomOrientationCSSClass);
        }

        this.item.setStyle({
            'left': left + "px",
            'top': top + "px"
        });
    }
};