(function ($) {

    var defaults = {
        orderIdTemplate:        'order_id_template',
        orderItemTemplate:      'order_item_template',
        offlineItems:           'offline_items',
        offlineOrderInput:      'UI-OFFLINE-ORDER-INPUT'
    };

    jQuery.fn.offlineOrder = function (params) {
        var $body = $('body');
        var $this = this;
        var options = $.extend({}, defaults, params);

        var init = function () {
            options.addNewOfflineOrderButton = 'button.add_new_offline_order';
            options.addNewOfflineItem = '.add_offline_item';
            options.deleteOfflineItem = '.delete_offline_item';
            options.deleteOfflineOrder = '.delete_offline_order';

            $body.on('click', options.addNewOfflineOrderButton, function () {
                addOrder();
            });

            $body.on('click', options.addNewOfflineItem, function () {
                options.orderContainer = $(this).parents('.UI-ORDER-CONTAINER');
                addItem();
            });

            $body.on('click', options.deleteOfflineItem, function () {
                var $itemBlock = $(this).parents('.UI-OFFLINE-PRODUCT');
                var $orderBlock = $itemBlock.parents('.'+options.offlineItems);

                $itemBlock.remove();

                $(options.addNewOfflineItem, $orderBlock).last().show();
                if ($('.UI-OFFLINE-PRODUCT', $orderBlock).length < 2) {
                    $('.UI-OFFLINE-PRODUCT ' + options.deleteOfflineItem, $orderBlock).hide();
                } else {
                    $('.UI-OFFLINE-PRODUCT ' + options.deleteOfflineItem, $orderBlock).show();
                }
            });

            $body.on('click', options.deleteOfflineOrder, function () {
                $(this).parents('.UI-ORDER-CONTAINER').remove();
                if ($('.UI-ORDER-CONTAINER').length < 2) {
                    $(options.deleteOfflineOrder).hide();
                } else {
                    $(options.deleteOfflineOrder).show();
                }
            });

            $body.on('change', '.'+options.offlineOrderInput, function () {
                updateIds($(this).parents('.UI-ORDER-CONTAINER'));
            });

            $body.on('change', '.UI-ITEMNAME', function () {
                updateIds($(this).parents('.UI-ORDER-CONTAINER'));
            });

            $body.on('change', '.UI-ITEMQTY', function () {
                updateIds($(this).parents('.UI-ORDER-CONTAINER'));
            });

        };

        var addOrder = function () {
            $($this).append($('#'+options.orderIdTemplate).html());

            options.orderContainer = $('.UI-ORDER-CONTAINER', $this).last();
            if ($('.UI-ORDER-CONTAINER').length < 2) {
                $(options.deleteOfflineOrder)[0].hide();
            } else {
                $(options.deleteOfflineOrder)[0].show();
            }

            addItem();
        }

        var addItem = function () {
            $('.' + options.offlineItems, options.orderContainer).append($('#'+options.orderItemTemplate).html());
            if ($('.' + options.offlineItems + ' .UI-OFFLINE-PRODUCT', options.orderContainer).length < 2) {
                $('.' + options.offlineItems + ' .UI-OFFLINE-PRODUCT ' + options.deleteOfflineItem,
                    options.orderContainer).hide();
            } else {
                $('.' + options.offlineItems + ' .UI-OFFLINE-PRODUCT ' + options.deleteOfflineItem,
                    options.orderContainer).show();
            }
        }

        var updateIds = function (el) {
            var orderId = $('.'+options.offlineOrderInput, el).val();
            var itemOrderId = orderId;

            $('.UI-ORDER-ID-INPUT', el).attr('name', 'order_id[]').val(orderId);
            $('.'+options.offlineItems+' > tr', el).each(function () {

                var itemNameField = $('.UI-ITEMNAME', this);
                var itemName = $(itemNameField).val();

                $(itemNameField).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][name]');

                $('.UI-OFFLINE-SELECT-ORDER', this).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][checkbox]').val(orderId);
                $('.UI-IS-OFFLINE', this).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][is_offline]').val(orderId);

                var itemField = $('.UI-ORDER-ID', this);
                $(itemField, el).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][receipt_number]').val(orderId);

                var itemField = $('.UI-ITEM-ID', this);
                $(itemField).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][order_item_id]');
                $(itemField).val(itemName);
                var itemField = $('.UI-ITEMQTY', this);
                $(itemField).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][qty_requested]');
                var itemField = $('.UI-REASON', this);
                $(itemField).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][reason_id]');
                var itemField = $('.UI-CONDITION', this);
                $(itemField).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][condition_id]');
                var itemField = $('.UI-RESOLUTION', this);
                $(itemField).attr('name', 'offline_items[' + itemOrderId + '][' + itemName + '][resolution_id]');
            });
        }

        return this.each(init);
    }

    $(document).ready(function ($) {
        $('#add_new_offline_order_container').offlineOrder();
        $ordersAmount = $('#add_new_offline_order_container > .entry-edit').length + $('#add_new_order_container > .entry-edit').length;
        if (!$ordersAmount) {
            $('.add_new_offline_order').click();
        }
    });
}) (jQuery);