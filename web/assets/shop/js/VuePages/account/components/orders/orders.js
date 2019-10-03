var AccountOrders = {
    data: function () {
        return {
            orders: [],
            loading: false,
            payPalWindow: {
                'open': false,
                'id': ''
            }
        }
    },
    mounted: function () {
        this.loadOrders();
        var _that = this;
        EventBus.$on('ordersResponse', function (orders) {
            if (orders) {
                _that.orders = orders;
            }
        });
    },
    methods: {
        loadOrders: function () {
            var that = this;
            getRequest('user/orders')
                .then(data => {
                    that.orders = data;
                })
                .catch(error => {});
        },        
        showAdons: function (o, i) {
            if (this.orders[o].orderItems[i].addons) {
                if (this.orders[o].orderItems[i].addons.length > 0) {
                    return true;
                }
            }
            if (this.orders[o].orderItems[i].includedAddons) {
                if (this.orders[o].orderItems[i].includedAddons.length > 0) {
                    return true;
                }
            }
            return false;
        },
        openPaymentWindow: function (orderId, url) {
            let that = this;
            if (!!url) {
                $('.bt.modal.pay').modal('show');
                if (this.payPalWindow.open && this.payPalWindow.id === orderId) {
                    PayPalWindow.focus();
                } else {
                    that.openPayPalWindow(orderId, url);
                    var timer = setInterval(function () {
                        if (!that.loading && that.payPalWindow.open) {
                            that.checkPaymentStatus(orderId);
                        }
                        if (PayPalWindow && PayPalWindow.closed) {
                            clearInterval(timer);
                            if (that.payPalWindow.open) {
                                $('.bt.modal.pay').modal('hide');
                                that.payPalWindow.open = false;
                            }
                        }
                    }, 500);
                }
            }
        },
        checkPaymentStatus: function (orderId) {
            let that = this;
            this.loading = true;
            getRequest('order/' + orderId + '/payment-status')
                .then(response => {
                    that.loading = false;
                    if (!!response.paymentStatus) {
                        if (response.paymentStatus === 'completed') {
                            $('.bt.modal.pay').modal('hide');
                            that.loadOrders();
                            that.payPalWindow.open = false;
                            if (!PayPalWindow.closed) {
                                $('.bt.modal.pay').modal('hide');
                                PayPalWindow.close();
                            }
                        }
                    }
                });
        },
        openPayPalWindow: function (orderId, url) {
            if (this.payPalWindow.open && this.payPalWindow.id != orderId) {
                PayPalWindow.close();
                this.payPalWindow.open = false;
            }
            this.payPalWindow.id = orderId;
            this.payPalWindow.open = true;
            PayPalWindow = window.open(url, "_blank ", "width=500,height=600");
            PayPalWindow.moveBy((window.innerWidth - 500) / 2, (window.innerHeight - 600) / 2);
        },
        showWindowO: function () {
            PayPalWindow.focus();
        }
    },
}