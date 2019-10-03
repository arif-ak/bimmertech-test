$(window).on("load", function () {
    EventBus.$emit('documentReady');
});
$(document).ready(function () {
    // $('.ui.dropdown').dropdown()
    $('body').addClass('block')
    $(document).previewUploadedImage('#sylius_shipment_images_images');
    $(document).previewUploadedImage('#sylius_product_buyersOption');
    $(document).previewUploadedImage('#sylius_product_buyersImage');
    $(document).previewUploadedImage('#sylius_product_buyersImage');
    $(document).previewUploadedImage('#sylius_product_property_images');
    $(document).previewUploadedImage('#sylius_product_productDescriptions_0_images');
    $(document).previewUploadedImage('#sylius_product_productInstallers_0_images');
    $(document).previewUploadedImage('#app_bundle_dealer_type_images');
    $(document).previewUploadedImage('#blog_post_blogPostContent');
    $(document).previewUploadedImage('#blog_post_blogPostImage');

    function setScrollStorage() {
        var scrollData = {
            url: window.location.href,
            scrollTop: $(document).scrollTop()
        }
        window.localStorage.setItem('scroll', JSON.stringify(scrollData));
    }

    var scroll = JSON.parse(window.localStorage.getItem('scroll'));
    if (scroll) {
        if (scroll.url == window.location.href) {
            $(document).scrollTop(scroll.scrollTop)
        } else {
            setScrollStorage();
        }
    } else {
        setScrollStorage();
    }
    $(document).scroll(function () {
        setScrollStorage();
    });

});

var myMap,
    mirror_items_retG,
    modalCheckbox,
    windowInfo;



function emitMoodal(data) {
    let params = {
        edit: true,
        id: data
    };
    EventBus.$emit("refundsModal", params);
}

function admin_modal_logistic(e) {
    $('.ui.modal.logistic' + e).modal('show');
}

function admin_modal_logistic_shipment(e) {
    $('.ui.modal.logisticShipment' + e).modal('show');
}

function admin_support_instruction(e) {
    $('.admin_support_instruction.item' + e).show();
    $('.admin_support_instruction_info.item' + e).hide()
}

function admin_support_coding(e) {
    $('.admin_coding.item' + e).show();
    $('.admin_coding_info.item' + e).hide()
}

function admin_logistic_warehouse(event, item) {
    event.preventDefault();
    $('.admin_logistic_warehouse.item' + item).show();
    $('.admin_logistic_warehouse_info.item' + item).hide()
}

function edit_order_vin_show() {
    $('.order-vin').hide();
    $('.order-vin-edit').css('visibility', 'visible');
}

function edit_order_state_show(e) {
    $('.order-' + e + '-state').hide();
    $('.order-' + e + '-state-edit').css('display', 'block');
}

function sendInstruction(e) {
    var instruction = $('#inst_' + e).val();
    $('#inst_' + e).val('_' + instruction);
    $('#form_instruction_' + e).submit();
}

function readURL(input) {
    var parent = $(input).parent().parent();
    console.log(parent);
    var img = $('img', parent);
    // console.log(img);

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(img)
                .attr('src', e.target.result)
                .width(150)
                .height(112);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeBlog(input) {
    var parent = $(input).parent();
    var mainParent = parent.parent();
    var imgInner = $('img', parent);
    var imgOuter = $('img', mainParent);
    var inputFile = $('input[type="file"]', mainParent);
    var inputHidden = $('input[type="hidden"]', mainParent);
    console.log(inputFile);
    var removeButton = $('.remove-image', parent);

    imgInner.remove();
    imgOuter.remove();
    inputFile.val("");
    inputHidden.val('removed');
    removeButton.hide();
}

function addImage(input) {
    removeBlog(input);
    console.log(input);
    var parentBlock = input;
    var parent = $(parentBlock).parent();
    var mainParent = parent.parent();
    var inputHidden = $('input[type="hidden"]', mainParent);
    inputHidden.val('');

    console.log(parent);

    var removeButton = $('.remove-image', parent);
    removeButton.show();
    removeButton.style.display = "block"
}

function enableChannelPrice(e) {
    let _price = $('#sylius_product_variant_channelPricings_' + e.value + '_price'),
        _originalPrice = $('#sylius_product_variant_channelPricings_' + e.value + '_originalPrice');
    if ($(e)[0].checked) {
        _price[0].disabled = false;
        _originalPrice[0].disabled = false;
    } else {
        _price[0].disabled = true;
        _originalPrice[0].disabled = true;
    }

}

function addBuyersImage() {
    $('#sylius_product_buyersImage_0_file').val(null);
    $('#buyers-small-image').attr('src', '');

    if (image) {
        $.ajax({
            url: '/admin/buers-guide-image-remove/' + image,
            type: 'DELETE',
            success: function (result) {
                console.log(result)
            }
        });
    }
}

function orderUserRole() {
    $.ajax({
        url: '/admin/api2/order/index/role',
        type: 'GET',
        success: function (result) {
            $('.table  > tbody').html(result);
            $('.pagination').remove();
            $('.ui.two.column.fluid.stackable.grid').remove();
        }
    });
}


function modalCheckFun(el, action) {
    let $childCheckbox = $(el).closest('.checkbox').siblings('.list').find('.checkbox');
    $childCheckbox.each(function () {
        if ($(this).checkbox('can change')) {
            $(this).checkbox(action);
        }
    });
}

function modalCheckFunGlob() {
    $('.item-list .master.checkbox')
        .checkbox({
            onChecked: function () {
                modalCheckFun(this, 'check');
            },
            onUnchecked: function () {
                modalCheckFun(this, 'uncheck');
            }
        });
    $('.item-list .child.checkbox')
        .checkbox({
            fireOnInit: true
        });
}

function capitalize(data) {
    return data.charAt(0).toLocaleUpperCase().concat(data.slice(1));
}

function makeHistoryMessage(data, action, option) {
    action = parseAction((!action) ? data.action : action);
    switch (data.template) {
        case "SendViaEmail":
            return parse(data, 'usb', action);
        case "EditTrackingNumber":
            if (data.modalLoadedData) {
                modalLoadedData = (!isEmptyArray(data.modalLoadedData)) ? data.modalLoadedData.find(x => x.id === data.selected) : [];
            }
            return `${action} ${parse(modalLoadedData, 'courier')} shipping label ${data.tracking_number} ${forProd(parse(data.order_item_units, 'products'))}.`;
        case "SendInstructions":
            linkToInstruction = (data.sendViaEmailRadio) ? 'Sent via email' : data.linkToInstruction;
            return parse(data, 'instructions', action);
        case "ReturnedRefunded":
            return parse(data, action, option);
        case "changeWarehouse":
            return `Changed the warehouse from ${data.currentWarehouse} to ${data.selectedWarehouse} ${forProd(data.selectedItems)}.`;
        case "shipmentCreate":
            return `${action} ${data.name} shipping label ${data.number} ${forProd(data.products)}.`;
        case "orderStatus":
            return `Changed ${data.statusType} status from ${data.initialStatuses} to ${data.selectedStatus}.`;
    }

    function forProd(data) {
        return `for products: ${data}`;
    }



    function parse(data, option, state) {
        let result = new Map();
        if (option == "usb") {
            edit = (action == 'Edited') ? 'sent ' : '';
            return `${action} ${edit}USB Coding ${forProd(parse(data.items, 'products'))}.`;
        }
        if (option == "products") {
            return data.map(key => key.product_name).join(', ');
        }
        if (option == "instructions") {
            let products = [];
            if (data.header == 'Edit instructions') {
                action = 'Edited';
                edit = 'was changed to';
            } else {
                edit = 'for instruction';
            }
            data.selected.forEach(key => products.push(data.items.find(x => x.id === key).name));
            return `${action} instruction ${forProd(products.join(', '))}; the link ${edit}: ${linkToInstruction}.`;
        }
        if (option == "courier") {
            return isDef(data.courier) ? data.courier : data.ship_method.name;
        }
        if (option == "refund") {
            let returned = '',
                refunded = '';
            if (data.amount !== 0) {
                refunded = ` amount ${data.amount}$ ${forProd(data.selectedItemsRef)}.`;
            }
            if (data.selectedItemsRet !== '') {
                if (data.amount !== 0) {
                    if (state) {
                        returned = `products: ${data.selectedItemsRet}.`;
                    } else {
                        returned = `Returned products: ${data.selectedItemsRet}.`;
                    }
                } else {
                    returned = `${forProd(data.selectedItemsRet)}.`;
                }
            }
            if (state) {
                return `Edited Returned/Refunded ${returned} reason: ${data.reason}`;
            } else {
                return `Returned/Refunded${refunded} ${returned} Added the reason: ${data.reason}`;
            }
        }
    }

    function parseAction(data) {
        if (data == "order/usb-coding-create" || data == "order/add-instruction") {
            return 'Sent';
        }
        if (data == "update-order-shipment" || data == "order/usb-coding-update" || data == "refund-update") {
            return 'Edited';
        }
        if (data == "remove-shipment") {
            return 'Deleted';
        }
        if (data == "refund-create") {
            return;
        } else {
            return data;
        }
    }

}

function getNamesFromMapById(arrayId, map) {
    let names = '';
    arrayId.forEach(element => {
        names = names.concat(map.get(element).product_name, ', ');
    });
    return names.trim().slice(0, -1);
}
