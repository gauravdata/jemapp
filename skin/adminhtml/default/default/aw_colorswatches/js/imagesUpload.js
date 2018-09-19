(function(){
    var originalDeactivateFn = Droppables.deactivate.bind(Droppables);
    Droppables.deactivate = function(drop) {
        originalDeactivateFn(drop);
        if(drop.onDeactivate) drop.onDeactivate();
    };
    var originalActivateFn = Droppables.activate.bind(Droppables);
    Droppables.activate = function(drop) {
        originalActivateFn(drop);
        if(drop.onActivate) drop.onActivate();
    };
    //fix for 1.4.1.1 magento
    Draggables.register = function(draggable) {
        if(this.drags.length == 0) {
            this.eventMouseUp   = this.endDrag.bindAsEventListener(this);
            this.eventMouseMove = this.updateDrag.bindAsEventListener(this);
            this.eventKeypress  = this.keyPress.bindAsEventListener(this);

            Event.observe(document, "mouseup", this.eventMouseUp);
            Event.observe(document, "mousemove", this.eventMouseMove);
            Event.observe(document, "keypress", this.eventKeypress);
        }
        this.drags.push(draggable);
    };
})();

var AWColorswatchesImageUpload = Class.create();
AWColorswatchesImageUpload.prototype = {
    _gridItemTemplate: "<div class='awcolorswatch-images-grid-item' data-id='{{id}}'>" +
            "<div class='awcolorswatch-images-grid-item-remove' title='{{removeTitle}}'></div>" +
            "<div class='awcolorswatch-images-grid-item-title'>{{title}}</div>" +
            "<div class='awcolorswatch-images-grid-item-undo' style='display:none'><span>{{undoTitle}}</span></div>" +
            "<div class='awcolorswatch-images-grid-item-content' data-src='{{bkgSrc}}' data-original-src='{{bkgSrc}}'></div>" +
        "</div>",
    _gridItemCSSClass: "awcolorswatch-images-grid-item",
    _gridItemContentCSSClass: "awcolorswatch-images-grid-item-content",
    _gridItemUndoCSSClass: "awcolorswatch-images-grid-item-undo",
    _gridItemRemoveCSSClass: "awcolorswatch-images-grid-item-remove",
    _gridItemStateCSSClassList: {
        full: "awcolorswatch-images-grid-item-ddstate-full",
        available: "awcolorswatch-images-grid-item-ddstate-available",
        complete: "awcolorswatch-images-grid-item-ddstate-complete"
    },

    _overlayCSSClass: "awcolorswatch-images-container-overlay",
    _overlayWrapperContentCSSClass: "awcolorswatch-images-container-overlay-wrapper-content",

    _progressBarTpl: "<div class='awcolorswatch-images-container-overlay'>" +
            "<div class='awcolorswatch-images-container-overlay-wrapper'><div class='awcolorswatch-images-container-overlay-wrapper-content'>" +
                "<div class='awcolorswatch-images-container-overlay__progressbar-title'>{{title}} (<span>{{percent}}</span>)</div>" +
                "<div class='awcolorswatch-images-container-overlay__progressbar-visual'>" +
                    "<div class='awcolorswatch-images-container-overlay__progressbar-visual-uploaded'></div>" +
                "</div>" +
            "</div></div>" +
        "</div>",
    _progressBarTitleCSSClass: "awcolorswatch-images-container-overlay__progressbar-title",
    _progressBarVisualCSSClass: "awcolorswatch-images-container-overlay__progressbar-visual",
    _progressBarVisualUploadedCSSClass: "awcolorswatch-images-container-overlay__progressbar-visual-uploaded",

    _resultViewTpl: "<div class='awcolorswatch-images-container-overlay'>" +
            "<div class='awcolorswatch-images-container-overlay-wrapper'><div class='awcolorswatch-images-container-overlay-wrapper-content'>" +
                "<div class='awcolorswatch-images-container-overlay__result-success-title'>{{successTitle}}</div>" +
                "<div class='awcolorswatch-images-container-overlay__result-failure-title'>{{failureTitle}}</div>" +
                "<div class='awcolorswatch-images-container-overlay__result-btn'>{{btnTitle}}</div>" +
            "</div></div>" +
        "</div>",
    _resultViewBtnCSSClass: "awcolorswatch-images-container-overlay__result-btn",

    _ddListItemTpl: "<div class='awcolorswatch-dd-list-item'>" +
            "<div class='awcolorswatch-dd-list-item-content' data-src='{{bkgSrc}}'></div>" +
        "</div>",
    _ddListItemCSSClass: "awcolorswatch-dd-list-item",
    _ddListItemContentCSSClass: "awcolorswatch-dd-list-item-content",
    _ddListItemStateCSSClassList: {
        dragged: "awcolorswatch-dd-list-item-content-ddstate-dragged"
    },

    _uploadProgressInfo: {},

    initialize: function(config){
        this.containerEl = $$(config.containerSelector).first();
        this.gridEl = $$(config.gridSelector).first();
        this.attachmentAreaEl = $$(config.attachmentAreaSelector).first();
        this.attachmentAreaDragoverCSSClass = config.attachmentAreaDragoverCSSClass;
        this.ddListEl = $$(config.ddListSelector).first();
        this.uploadUrl = config.uploadUrl;
        this.secureFormKey = config.secureFormKey;
        this.dataInputSelector = config.dataInputSelector;
        this.dataInputNameTemplate = config.dataInputNameTemplate;

        this.titles = config.titles;
        this.data = config.data;

        this.init();
        this.startObservers();
    },

    init: function() {
        var me = this;
        this.data.each(function(data){
            me._addGridItem(data.id, data.title, data.value);
        });
        this.onResize();
        this._attachElToWindowTop(this.ddListEl);
    },

    startObservers: function() {
        var me = this;
        Event.observe(window, 'resize', this.onResize.bind(this));
        if (!Object.isUndefined(varienGlobalEvents)) {
            varienGlobalEvents.attachEventHandler('showTab', this.onResize.bind(this));
        }
        this.gridEl.select('.' + this._gridItemCSSClass).each(function(item){
            var undoEl = item.select('.' + me._gridItemUndoCSSClass).first();
            undoEl.observe('click', function(e){
                me.onUndo(item);
            });
            var removeEl = item.select('.' + me._gridItemRemoveCSSClass).first();
            removeEl.observe('click', function(e){
                me.onRemove(item);
            });
        });
        this.attachmentAreaEl.observe('dragover', function(e){
            me.attachmentAreaEl.addClassName(me.attachmentAreaDragoverCSSClass);
            e.dataTransfer.dropEffect = 'move';
            e.stop();
        });
        this.attachmentAreaEl.observe('dragleave', function(e){
            me.attachmentAreaEl.removeClassName(me.attachmentAreaDragoverCSSClass);
        });
        this.attachmentAreaEl.observe('dragend', function(e){
            me.attachmentAreaEl.removeClassName(me.attachmentAreaDragoverCSSClass);
        });
        this.attachmentAreaEl.observe('drop', function(e){
            me.attachmentAreaEl.removeClassName(me.attachmentAreaDragoverCSSClass);
            e.stop();
            me.onFileDrop(e.dataTransfer.files);
        });
        var fileInput = this.attachmentAreaEl.select('input[type="file"]').first();
        this.attachmentAreaEl.observe('click', function(e){
            fileInput.click();
        });
        fileInput.observe('change', function(e){
            e.stop();
            me.onFileDrop(fileInput.files);
            fileInput.value = "";
        });
    },

    onResize: function() {
        this.gridEl.setStyle({width: null});
        var gridWidth = this.gridEl.getWidth();
        var gridItem = this.gridEl.select('.' + this._gridItemCSSClass).first();
        if (!gridItem) {
            return;
        }
        var gridItemWidth = gridItem.getWidth();
        var count = parseInt(gridWidth/gridItemWidth);
        count = Math.min(count, this.gridEl.select('.' + this._gridItemCSSClass).length);
        this.gridEl.setStyle({
            'width': (count * gridItemWidth) + "px"
        });
    },

    onUndo: function(item) {
        var contentEl = item.select('.' + this._gridItemContentCSSClass).first();
        this._addDragDropItem(contentEl.getAttribute('data-src'));//move current image to right area
        this.markItemAsFull(item, contentEl.getAttribute('data-original-src'));
        var undoEl = item.select('.' + this._gridItemUndoCSSClass).first();
        undoEl.hide();
    },

    onRemove: function(item) {
        var contentEl = item.select('.' + this._gridItemContentCSSClass).first();
        this._addDragDropItem(contentEl.getAttribute('data-src'));//move current image to right area
        if (contentEl.getAttribute('data-original-src')
            && contentEl.getAttribute('data-src') !== contentEl.getAttribute('data-original-src')) {
            this._addDragDropItem(contentEl.getAttribute('data-original-src'));//move original image to right area
        }
        this.markItemAsEmpty(item);
        var undoEl = item.select('.' + this._gridItemUndoCSSClass).first();
        undoEl.hide();
        this._dataInputRecollect();
    },

    onFileDrop: function(files) {
        var me = this;
        if (files.length <= 0) {
            return;
        }
        this._addProgressBar();
        this._uploadProgressInfo = {};
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            this._uploadProgressInfo[i] = {
                "size": file.size,
                "uploaded_size": 0,
                "result": null
            };
            var successFn = (function(me, i){
                return function(json){
                    if (Object.isUndefined(me._uploadProgressInfo[i])) {
                        return;
                    }
                    me._uploadProgressInfo[i].uploaded_size = me._uploadProgressInfo[i].size;
                    me._uploadProgressInfo[i].result = true;
                    me._uploadProgressInfo[i].srcUrl = json.url;
                    me._updateProgressBar();
                };
            })(me, i);
            var failureFn = (function(me, i){
                return function(json){
                    if (Object.isUndefined(me._uploadProgressInfo[i])) {
                        return;
                    }
                    me._uploadProgressInfo[i].uploaded_size = me._uploadProgressInfo[i].size;
                    me._uploadProgressInfo[i].result = false;
                    me._updateProgressBar();
                };
            })(me, i);
            this.fileUpload(file, i, successFn, failureFn);
        }
    },

    markItemAsDropAvailable: function(item) {
        item
            .removeClassName(this._gridItemStateCSSClassList.full)
            .removeClassName(this._gridItemStateCSSClassList.complete)
        ;
        item.addClassName(this._gridItemStateCSSClassList.available);
        var contentEl = item.select('.' + this._gridItemContentCSSClass).first();
        contentEl.update(this.titles.availableDrop);
    },

    markItemAsDropComplete: function(item) {
        item
            .removeClassName(this._gridItemStateCSSClassList.full)
            .removeClassName(this._gridItemStateCSSClassList.available)
        ;
        item.addClassName(this._gridItemStateCSSClassList.complete);
        var contentEl = item.select('.' + this._gridItemContentCSSClass).first();
        contentEl.update(this.titles.completeDrop);
    },

    markItemAsEmpty: function(item) {
        item
            .removeClassName(this._gridItemStateCSSClassList.full)
            .removeClassName(this._gridItemStateCSSClassList.available)
            .removeClassName(this._gridItemStateCSSClassList.complete)
        ;
        var contentEl = item.select('.' + this._gridItemContentCSSClass).first();
        contentEl.setStyle({'backgroundImage': null});
        contentEl.setAttribute('data-src', '');
        contentEl.setAttribute('data-original-src', '');
        contentEl.update('');
    },

    markItemAsFull: function(item, backgroundSrc) {
        item
            .removeClassName(this._gridItemStateCSSClassList.available)
            .removeClassName(this._gridItemStateCSSClassList.complete)
        ;
        item.addClassName(this._gridItemStateCSSClassList.full);
        var contentEl = item.select('.' + this._gridItemContentCSSClass).first();
        contentEl.setStyle({'backgroundImage': 'url(' + backgroundSrc + ')'});
        contentEl.setAttribute('data-src', backgroundSrc);
        contentEl.update('');
        this._dataInputRecollect();
    },

    fileUpload: function(file, fileIndexInList, successFn, failureFn) {
        successFn = successFn||Prototype.emptyFunction;
        failureFn = failureFn||Prototype.emptyFunction;

        var formData = new FormData();
        formData.append('file', file, file.name);
        formData.append('form_key', this.secureFormKey);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.uploadUrl, true);
        xhr.onload = function () {
            if (xhr.status !== 200) {
                failureFn();
                return;
            }
            try {
                var json = xhr.responseText.evalJSON();
            } catch (e) {
                failureFn();
                return;
            }
            if (!json.success) {
                failureFn();
                return;
            }
            successFn(json);
        };
        xhr.onerror = function() {
            failureFn();
        };
        var me = this;
        xhr.upload.addEventListener("progress", function(e) {
            if (!e.lengthComputable) {
                return;
            }
            me._uploadProgressInfo[fileIndexInList].uploaded_size = e.loaded;
            me._updateProgressBar();
        }, false);
        xhr.send(formData);
    },

    _addGridItem: function(id, title, backgroundSrc) {
        var tpl = this._gridItemTemplate
            .replace(/{{id}}/g, id)
            .replace(/{{title}}/g, title)
            .replace(/{{undoTitle}}/g, this.titles.undo)
            .replace(/{{removeTitle}}/g, this.titles.removeTitle)
            .replace(/{{bkgSrc}}/g, backgroundSrc||'')
        ;
        var el = new Element('div', {});
        el.update(tpl);
        el = el.select('.' + this._gridItemCSSClass).first();
        if (backgroundSrc) {
            var contentEl = el.select('.' + this._gridItemContentCSSClass).first();
            contentEl.setStyle({'backgroundImage': 'url(' + backgroundSrc + ')'});
            el.addClassName(this._gridItemStateCSSClassList.full);
        }
        this.gridEl.appendChild(el);
        this._droppable(el);
    },

    _addDragDropItem: function(backgroundSrc) {
        var tpl = this._ddListItemTpl
            .replace(/{{bkgSrc}}/g, backgroundSrc||'')
        ;
        var el = new Element('div', {});
        el.update(tpl);
        el = el.select('.' + this._ddListItemCSSClass).first();
        if (backgroundSrc) {
            var contentEl = el.select('.' + this._ddListItemContentCSSClass).first();
            contentEl.setStyle({'backgroundImage': 'url(' + backgroundSrc + ')'});
        }
        if (this.ddListEl.down()) {
            this.ddListEl.down().insert({'before': el});
        } else {
            this.ddListEl.appendChild(el);
        }
        Event.fire(document, "awcsw:dd_added");
        this._draggable(el);
    },

    _addProgressBar: function() {
        var tpl = this._progressBarTpl
            .replace(/{{title}}/g, this.titles.progressBar)
            .replace(/{{percent}}/g, "0%")
        ;
        var el = new Element('div', {});
        el.update(tpl);
        var overlayEl = el.select('.' + this._overlayCSSClass).first();
        this.containerEl.appendChild(overlayEl);
        var wrapperContentEl = overlayEl.select('.' + this._overlayWrapperContentCSSClass).first();
        this._attachElToVerticalMiddleOfContainer(wrapperContentEl, overlayEl);
    },

    _removeProgressBar: function() {
        var overlayEl = this.containerEl.select('.' + this._overlayCSSClass).first();
        overlayEl._stopObservingFns.each(function(fn){
            fn();
        });
        overlayEl.remove();
    },

    _updateProgressBar: function() {
        var allSize = 0, uploadedSize = 0;
        var allCount = Object.values(this._uploadProgressInfo).length, proceedCount = 0;
        Object.values(this._uploadProgressInfo).each(function(item){
            allSize += item.size;
            uploadedSize += Math.min(item.uploaded_size, item.size);
            if (null !== item.result) {
                proceedCount++;
            }
        });
        if (allSize <= 0) {
            return;
        }
        //update percent info on view
        var percent = parseInt(uploadedSize * 100 / allSize);
        var progressTitleEl = this.containerEl.select('.' + this._progressBarTitleCSSClass).first();
        if (!progressTitleEl) {
            return;
        }
        progressTitleEl.select('span').first().update(percent + "%");
        var uploadedVisualProgress = this.containerEl.select('.' + this._progressBarVisualUploadedCSSClass).first();
        uploadedVisualProgress.setStyle({width: percent + "%"});
        if (allCount <= proceedCount) {
            this._removeProgressBar();
            this._addResultUploadView();
            var me = this;
            Object.values(this._uploadProgressInfo).each(function(item){
                if (item.result !== true) {
                    return;
                }
                me._addDragDropItem(item.srcUrl);
            });
        }
    },

    _addResultUploadView: function() {
        var successCount = 0, failureCount = 0;
        Object.values(this._uploadProgressInfo).each(function(item){
            item.result?successCount++:failureCount++;
        });
        var successTitle = (successCount > 0)?this.titles.uploadResultSuccessMsg.replace("%d", successCount):"";
        var failureTitle = (failureCount > 0)?this.titles.uploadResultFailureMsg.replace("%d", failureCount):"";
        var tpl = this._resultViewTpl
            .replace(/{{btnTitle}}/g, this.titles.uploadResultBtn)
            .replace(/{{successTitle}}/g, successTitle)
            .replace(/{{failureTitle}}/g, failureTitle)
        ;

        var el = new Element('div', {});
        el.update(tpl);
        var overlayEl = el.select('.' + this._overlayCSSClass).first();
        this.containerEl.appendChild(overlayEl);
        var wrapperContentEl = overlayEl.select('.' + this._overlayWrapperContentCSSClass).first();
        this._attachElToVerticalMiddleOfContainer(wrapperContentEl, overlayEl);

        var me = this;
        overlayEl.select('.' + this._resultViewBtnCSSClass).first().observe('click', function(e){
            overlayEl._stopObservingFns.each(function(fn){
                fn();
            });
            overlayEl.remove();
        });
    },

    _attachElToVerticalMiddleOfContainer: function(el, container) {
        var fn = function() {
            var containerOffset = container.cumulativeOffset().top - container.cumulativeScrollOffset().top;
            var docHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
            var visibleContainerHeight = docHeight - Math.max(containerOffset, 0);
            visibleContainerHeight = Math.min(visibleContainerHeight, container.getHeight());
            var elTopOffset = (visibleContainerHeight/2 - el.getHeight()/2) - Math.min(containerOffset, 0);
            el.setStyle({top: elTopOffset + "px"});
        };
        fn();
        Event.observe(document, 'scroll', fn);
        Event.observe(window, 'resize', fn);
        Event.observe(document, 'awcsw:dd_added', fn);
        container._stopObservingFns = [
            function(){Event.stopObserving(document, 'scroll', fn)},
            function(){Event.stopObserving(window, 'resize', fn)},
            function(){Event.stopObserving(document, 'awcsw:dd_added', fn)}
        ];
    },

    _attachElToWindowTop: function(el) {
        var container = this.containerEl;
        var originalElOffsetTop;
        var fn = function() {
            var containerOffset = container.cumulativeOffset().top - container.cumulativeScrollOffset().top;
            var docHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
            var visibleContainerHeight = docHeight - Math.max(containerOffset, 0);
            el.setStyle({'maxHeight': null});
            maxElHeight = Math.min(visibleContainerHeight, container.getHeight(), el.getHeight());
            el.setStyle({'maxHeight': maxElHeight + "px"});

            if (parseInt(el.getStyle('marginTop')) === 0) {
                originalElOffsetTop = el.cumulativeOffset().top - container.cumulativeOffset().top;
            }
            var bottomBoundary = container.cumulativeOffset().top + container.getHeight();
            var elBottom = el.getHeight() + el.cumulativeOffset().top;
            var elTopOffset = el.cumulativeOffset().top - (parseFloat(el.getStyle('marginTop'))||0);
            //37 - this is a .content-header-floating element
            var newTop = container.cumulativeScrollOffset().top - container.cumulativeOffset().top - originalElOffsetTop + 37;
            newTop = Math.max(newTop, 0);
            var newElBottom = elBottom - parseFloat(el.getStyle('marginTop')) + newTop;
            if (bottomBoundary < newElBottom) {
                newTop -= newElBottom - bottomBoundary;
            }
            if (newTop > 0) {
                el.setStyle({marginTop: newTop + "px"});
            } else {
                el.setStyle({marginTop: null});
            }
        };
        fn();
        Event.observe(document, 'scroll', fn);
        Event.observe(window, 'resize', fn);
        Event.observe(document, 'awcsw:dd_added', fn);
    },

    _dataInputRecollect: function(){
        var data = {};
        var me = this;
        this.gridEl.select('.' + this._gridItemCSSClass).each(function(item){
            var id = parseInt(item.getAttribute('data-id'));
            var inputEl = $$(
                me.dataInputSelector + "[name='" + me.dataInputNameTemplate.replace('%d', id) + "']"
            ).first();
            inputEl.setValue(
                encodeURIComponent(
                    item.select('.' + me._gridItemContentCSSClass).first().getAttribute('data-src')
                )
            );
        });
    },

    _draggable: function(ddItemEl){
        var el = ddItemEl.select('.' + this._ddListItemContentCSSClass).first();
        var me = this;
        var dd = null;
        dd = new Draggable(el, {
            revert: true,
            scroll: window,
            ghosting: true,
            onStart: function() {
                dd._clone.addClassName(me._ddListItemStateCSSClassList.dragged);
                me.gridEl.select('.' + me._gridItemCSSClass).each(function(item){
                    if (!item.hasClassName(me._gridItemStateCSSClassList.full)) {
                        me.markItemAsDropAvailable(item);
                    }
                });
            },
            onEnd: function() {
                el.setStyle({'position': null});
                me.gridEl.select('.' + me._gridItemStateCSSClassList.available).each(function(item){
                    me.markItemAsEmpty(item);
                });
            },
            change: function (dragObject) {
                var top = Math.max(parseInt(el.getStyle('top')), 0);
                var left = Math.max(parseInt(el.getStyle('left')), 0);
                var maxTop = me.containerEl.getHeight() - el.getHeight();
                var maxLeft = me.containerEl.getWidth() - el.getWidth();
                el.setStyle({
                    'left': Math.min(left, maxLeft) + "px",
                    'top': Math.min(top, maxTop) + "px"
                });
            },
            onDropped: function (el) {
                dd.destroy();
                ddItemEl.remove();
            }
        });
    },

    _droppable: function(el) {
        var me = this;
        Droppables.add(el, {
            onActivate: function() {
                this._lastItemState = 'available';
                if (el.hasClassName(me._gridItemStateCSSClassList.full)) {
                    this._lastItemState = 'full';
                }
                me.markItemAsDropComplete(el);
            },
            onDeactivate: function() {
                if (this._lastItemState === 'available') {
                    me.markItemAsDropAvailable(el);
                } else if(this._lastItemState === 'full') {
                    var contentEl = el.select('.' + me._gridItemContentCSSClass).first();
                    me.markItemAsFull(el, contentEl.getAttribute('data-src'));
                }
                this._lastItemState = null;
            },
            onDrop: function(draggedEl, droppedEl, event) {
                this._lastItemState = null;
                var contentEl = el.select('.' + me._gridItemContentCSSClass).first();
                if (contentEl.getAttribute('data-original-src')) {
                    var undoEl = el.select('.' + me._gridItemUndoCSSClass).first();
                    undoEl.show();
                }
                if (contentEl.getAttribute('data-original-src') !== contentEl.getAttribute('data-src')) {
                    me._addDragDropItem(contentEl.getAttribute('data-src'));
                }
                me.markItemAsFull(el, draggedEl.getAttribute('data-src'));
            }
        });
    }
};