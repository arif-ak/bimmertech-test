if (window.location.pathname == "/order/thank-you") {
    window.location.href = '/thank-you';
}

var auditOnVisible = true;
// ***********************************************************
// Start apiConfig
// ***********************************************************
var apiConfig = {
    loadProduct: true,
    loadCart: false,
    checkLogin: true,
    modelsOption: true,
    getCarModel: true,
    loadMenu: false,
    loadReviews: false,
    loadBlogOnHomePage: true,
    loadRecomendedOnBlogListPage: true,
    loadRecomendedOnProduct: true,
    loadRecomendedOnHomePage: true,
    loadRecomendedOnTaxonPage: true,
    loadRecomendedOnBlogPage: true,
    loadRecomendedOnContainerPage: true
};
var imageConfig = {
    productListImg: false
};
var ignoreLocationOriginList = [
    'localhost',
    '127.0.0.1',
    '192.168.1.5'
]
var realServer = true;
for (let i = 0; i < ignoreLocationOriginList.length; i++) {
    if (window.location.origin=='http://'+ignoreLocationOriginList[i]+':8000' || window.location.origin=='https://'+ignoreLocationOriginList[i]+':8000') {
        realServer = false;
        break;
    }
}
if (realServer) {
    for (const key in apiConfig) {
        if (apiConfig.hasOwnProperty(key)) {
            apiConfig[key] = true;
        }
    }
}

if (window.location.pathname == '/') {
    $(window).on("load", function () {
        $('body').addClass('block');
        EventBus.$emit('documentReady', apiConfig);
    });
} else {
    $(document).ready(function () {
        $('body').addClass('block');
        EventBus.$emit('documentReady', apiConfig);
    });
}
// ***********************************************************
// End apiConfig
// ***********************************************************

$(document).ready(function () {

    setTimeout(() => {
        $('.product-page .bt-popup').popup({
            inline: true,
            position: 'top center',
            hoverable: true
        });

        $('.product-container .bt-popup').popup({
            inline: true,
            hoverable: true,
            position: 'top center'
        });
    }, 25);
    $('.btn-in-text.user-login-btn').click(function () {
        $('.user-login').popup('show');
    });
    $('.ui.cell.dropdown')
        .dropdown({
            // you can use any ui transition
            icon: '.chevron.down'
        });

    /* $('.bt.modal.askModal')
        .modal({
            selector: {
                close: '.bt-close, .actions .button'
            }
        })
        .modal('attach events', '.js-modal-expert', 'show'); */

    $('.bt.modal.thankForSubmission')
        .modal({
            selector: {
                close: '.bt-close, .actions .button'
            },
        });
    $('.bt.modal.pay')
        .modal({
            selector: {
                close: '.bt-close'
            },
            onHide: function () {
                PayPalWindow.close();
            },
            closable: false
        });
    $('.vin-check-identify').on('click', function (e) {
        $('.ui.menu.vin-check-service').click();
    });
    $('.bt-card .image.dimmable').dimmer({
        on: 'hover'
    });
    $('#main-slider').accordionSlider({
        width: '100%',
        height: '100vh',
        responsiveMode: 'auto',
        visiblePanels: 3,
        keyboard: false,
        startPanel: 0,
        shadow: false,
        closePanelsOnMouseOut: false,
        maxOpenedPanelSize: '50%',
        autoplay: false,
        mouseWheel: false
    });
    $('.bt.accordion').accordion({
        selector: {
            title: '.title',
            trigger: '.title',
            content: '.content'
        },
        className: {
            active: 'active',
            animating: 'animating'
        }
    });
    $('.hvr-ripple-out').click(function () {
        $(this).addClass('active');
        setTimeout((function () {
            $('.hvr-ripple-out').removeClass('active');
        }), 1000);
    });
    $('#mc-embedded-subscribe-form').subscribe();
    $.fn.iframe($('.js-iframe-resize iframe'));
});

function account_delete() {
    $('.ui.mini.modal.deleteAccount').modal('show');
}

function handleProductOptionsChange() {
    // TODO: change logic for checkboxes
    $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', function () {
        var $selector = '';
        $('#sylius-product-adding-to-cart div[data-option]').each(function () {
            var value = $(this)
                .find('label.menu label.selected')
                .attr('data-value');
            if (!value) {
                value = $(this)
                    .find('label.menu label')
                    .first()
                    .attr('data-value');
            }

            var option = $(this).attr('data-option');
            $selector += '[data-' + option + '="' + value + '"]';
        });

        var $price = $('#sylius-variants-pricing')
            .find($selector)
            .attr('data-value');

        if ($price !== undefined) {
            $('#product-price').text($price);
            $('button[type=submit]').removeAttr('disabled');
        } else {
            $('#product-price').text(
                $('#sylius-variants-pricing').attr('data-unavailable-text')
            );
            $('button[type=submit]').attr('disabled', 'disabled');
        }
    });
}

(function ($) {
    'use strict';
    $.fn.extend({
        subscribe: function () {
            var $form = $('#mc-embedded-subscribe-form');
            if ($form.length > 0) {
                $('form input[type="submit"]').bind('click', function (event) {
                    if (event)
                        event.preventDefault();
                    $.fn.register($form);
                });
            }
        },
    });
    $.fn.extend({
        register: function ($form) {
            console.log($('#mce-EMAIL').hasClass('untouched'));
            if (!$('#mce-EMAIL').hasClass('untouched')) {
                $('#mc-embedded-subscribe').val('Sending...').addClass('active');
                $.ajax({
                    type: $form.attr('method'),
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    cache: false,
                    dataType: 'json',
                    contentType: 'application/json; charset=utf-8',
                    error: function (err) {
                        $('#subscribe-result').val('Could not connect to the registration server. Please try again later.');
                        //alert('Could not connect to the registration server. Please try again later.')
                    },
                    success: function (data) {
                        if (data.result === 'success') {
                            $('#mce-EMAIL').css('borderColor', '#ffffff');
                            $('#subscribe-result').css('color', 'rgb(53, 114, 210)');
                            //$('#subscribe-result').html('<p>Thank you for subscribing. We have sent you a confirmation email.</p>')
                            //console.log(data.msg)
                            setTimeout((function () {
                                $('#mc-embedded-subscribe').val('Congrats! You are successfully subscribed.');
                            }), 1000);
                            setTimeout((function () {
                                $('#mc-embedded-subscribe').val('Subscribe').removeClass('active');
                                $('#mce-EMAIL').val('');
                            }), 5000); // Yeahhhh Success
                        } else {
                            // Something went wrong, do something to notify the user.
                            //console.log(data.msg)
                            setTimeout((function () {
                                $('#mc-embedded-subscribe').val('Oops! You are already subscribed.');
                            }), 1000);
                            $('#subscribe-result').css('color', '#f5350b');
                            setTimeout((function () {
                                $('#mc-embedded-subscribe').val('Subscribe').removeClass('active');
                            }), 5000);
                            //$('#subscribe-result').html('<p>' + data.msg.substring(4) + '</p>')
                        }
                    }
                });
            }
        }
    });
    $.fn.extend({
        compRes: function (el) {
            switch (el) {
                case 'Yes':
                    return 'green bt-yes';
                case 'No':
                    return 'red bt-no';
                case 'Not sure':
                    return 'blue bt-maybe';
                case '':
                    return 'yellow bt-attention';

            }
        }
    });
    $.fn.extend({
        iframe: function (nodeList) {
            if (!!nodeList) {
                for (let index = 0; index < nodeList.length; index++) {
                    nodeList[index].height = nodeList[index].width / 16 * 10;
                }
            }
        }
    });

})(jQuery);

var map = {
    82: false,
    83: false,
    17: false,
    16: false
};
$(document).keydown(function (e) {
    if (e.keyCode in map) {
        map[e.keyCode] = true;
    }
    if (map[82] && map[83] && map[17] && map[16]) {
        alert('rs');
        map = {
            82: false,
            83: false,
            17: false,
            16: false
        };
    }
}).keyup(function (e) {
    if (e.keyCode in map) {
        map[e.keyCode] = false;
    }
});