var Status = {
    props: ['access'],
    data: function () {
        const ORDER_CODDING_TITLE = 'Coding Board Status';
        const ORDER_SHIPMENT_TITLE = 'Shipping Status';
        const ORDER_PAYMENT_TITLE = 'Payment Status';
        const ORDER_SUPPORT_TITLE = 'Support Board Status';
        const ORDER_GENERAL_TITLE = 'Order Status';

        const ORDER_CODDING_STATE = 'codding';
        const ORDER_SHIPMENT_STATE = 'shipment';
        const ORDER_PAYMENT_STATE = 'payment';
        const ORDER_SUPPORT_STATE = 'support';
        const ORDER_GENERAL_STATE = 'order';

        const COMPLETE = 'completed';
        const NOT_REQUIRED = 'not required';
        const NOT_ADDED = 'not added';
        const NOT_CODED = 'not coded';
        const PARTIALLY_ADDED = 'partially added';
        const PARTIALLY_CODED = 'partially coded';

        const STATE_DELIVERED = 'delivered';
        const STATE_NOT_SHIPPED = "not shipped";
        const STATE_SHIPPED = 'shipped';
        const STATE_PARTIALLY_SHIPPED = "partially shipped";
        const STATE_BACK_ORDER = "backorder";

        const STATE_PAID = "paid";
        const STATE_AWAITING_PAYMENT = "awaiting_payment";
        const STATE_CANCELLED = 'cancelled';
        const STATE_REFUNDED = 'refunded';
        const STATE_PARTIALLY_REFUNDED = "partially refunded";

        const STATE_CART = 'cart';
        const STATE_READY = 'ready';
        const STATE_NEW = 'new';
        const STATE_FULFILLED = 'fulfilled';
        const STATUS_PLACED = 'placed';

        return {
            ORDER_CODDING_STATE: ORDER_CODDING_STATE,
            ORDER_SHIPMENT_STATE: ORDER_SHIPMENT_STATE,
            ORDER_PAYMENT_STATE: ORDER_PAYMENT_STATE,
            ORDER_SUPPORT_STATE: ORDER_SUPPORT_STATE,
            ORDER_GENERAL_STATE: ORDER_GENERAL_STATE,
            STATE_CART: STATE_CART,
            STATE_NEW: STATE_NEW,
            STATUS_PLACED: STATUS_PLACED,
            STATE_AWAITING_PAYMENT: STATE_AWAITING_PAYMENT,
            STATE_READY: STATE_READY,
            STATE_NOT_SHIPPED: STATE_NOT_SHIPPED,
            ORDER_GENERAL_TITLE: ORDER_GENERAL_TITLE,
            ORDER_SUPPORT_TITLE: ORDER_SUPPORT_TITLE,
            ORDER_CODDING_TITLE: ORDER_CODDING_TITLE,
            ORDER_SHIPMENT_TITLE: ORDER_SHIPMENT_TITLE,
            ORDER_PAYMENT_TITLE: ORDER_PAYMENT_TITLE,
            COMPLETE: COMPLETE,
            STATE_PAID: STATE_PAID,

            orderSupportStatuses: [{
                    value: NOT_ADDED
                },
                {
                    value: PARTIALLY_ADDED
                },
                {
                    value: NOT_REQUIRED
                },
                {
                    value: COMPLETE
                },
            ],

            orderCodingStatuses: [{
                    value: NOT_CODED
                },
                {
                    value: PARTIALLY_CODED
                },
                {
                    value: NOT_REQUIRED
                },
                {
                    value: COMPLETE
                },
            ],

            orderShipmentStatuses: [
                // {value: STATE_NOT_SHIPPED},
                // {value: STATE_PARTIALLY_SHIPPED},
                // {value: STATE_SHIPPED},
                // {value: STATE_DELIVERED},
                {
                    value: STATE_BACK_ORDER
                },
            ],

            orderPaymentStatuses: [{
                    value: STATE_CANCELLED
                },
                {
                    value: STATE_REFUNDED
                },
                {
                    value: STATE_PARTIALLY_REFUNDED
                },
                {
                    value: STATE_PAID
                },
            ],

            orderGeneralStatuses: [{
                    value: STATE_FULFILLED
                },
                {
                    value: STATE_CANCELLED
                },
                // {value: STATUS_PLACED},
            ],

            loading: false,
            initialStatuses: [],
            statuses: [],
            successMessage: false,
            compatibility: '',
            compatibilityClass: ''
        }
    },

    components: {
        'section-status': StatusAction,
    },

    mounted: function () {
        this.orderStatuses();
        EventBus.$on("reloadData", data => this.orderStatuses());
        EventBus.$on("load_order_status", () => {
            this.orderStatuses();
        });
    },

    methods: {
        orderStatuses: function () {
            var that = this;
            this.loading = true;
            this.$http.get('/admin/api2/order/' + orderId + '/states')
                .then(response => {
                    that.compatibility = response.data.data.compatibility;
                    that.compatibilityClass = convertToKebabCase(that.compatibility);
                    delete response.data.data.compatibility;
                    EventBus.$emit('updateVin', response.data.data);
                    delete response.data.data.vin;
                    that.initialStatuses = that.filterOrderStatuses(response.data.data);
                    that.statuses = that.filterOrderStatuses(response.data.data);
                }).catch(error => {
                    that.loading = false;
                }).finally(function () {
                    that.loading = false;
                });
        },

        filterOrderStatuses: function (statuses) {
            var that = this;
            var listOfStatuses = [];

            for (var variable in statuses) {
                listOfStatuses.push(that.getStatusByKey(variable, statuses[variable]));
            }

            return listOfStatuses;
        },
        getStatusByKey: function (type, selectedStatus) {
            var that = this;
            switch (type) {
                case "general_status":
                    return {
                        selectedStatus: {
                                value: that.filterSelectedStatus(type, selectedStatus)
                            },
                            listOfStatuses: that.orderGeneralStatuses,
                            statusType: this.ORDER_GENERAL_STATE,
                            title: 'Order Status',
                            class: convertToKebabCase(that.filterSelectedStatus(type, selectedStatus)),
                            changeStatusButton: true
                    };

                case "coding_status":
                    return {
                        selectedStatus: {
                                value: selectedStatus
                            },
                            listOfStatuses: that.orderCodingStatuses,
                            statusType: this.ORDER_CODDING_STATE,
                            title: 'Coding Board Status',
                            class: convertToKebabCase(selectedStatus),
                            changeStatusButton: false
                    };
                case "support_status":
                    return {
                        selectedStatus: {
                                value: selectedStatus
                            },
                            listOfStatuses: that.orderSupportStatuses,
                            statusType: this.ORDER_SUPPORT_STATE,
                            title: 'Support Board Status',
                            class: convertToKebabCase(selectedStatus),
                            changeStatusButton: false
                    };
                case "payment_status":
                    return {
                        selectedStatus: {
                                value: that.filterSelectedStatus(type, selectedStatus)
                            },
                            listOfStatuses: that.orderPaymentStatuses,
                            statusType: this.ORDER_PAYMENT_STATE,
                            title: 'Payment Status',
                            class: convertToKebabCase(that.filterSelectedStatus(type, selectedStatus)),
                            changeStatusButton: false
                    };
                case "shipment_status":
                    return {
                        selectedStatus: {
                                value: that.filterSelectedStatus(type, selectedStatus)
                            },
                            listOfStatuses: that.orderShipmentStatuses,
                            statusType: this.ORDER_SHIPMENT_STATE,
                            title: 'Shipping Status',
                            class: convertToKebabCase(that.filterSelectedStatus(type, selectedStatus)),
                            changeStatusButton: true
                    };
            }
        },

        filterSelectedStatus: function (type, selectedStatus) {
            var that = this;
            switch (type) {
                case "general_status":
                    if (selectedStatus == that.STATE_NEW || selectedStatus == that.STATE_CART) {
                        return that.STATUS_PLACED;
                    } else {
                        return selectedStatus;
                    }
                    case "payment_status":
                        if (selectedStatus == that.STATE_CART) {
                            return that.STATE_AWAITING_PAYMENT;
                        } else if (selectedStatus == that.COMPLETE) {
                            return that.STATE_PAID;
                        } else {
                            return selectedStatus;
                        }
                        case "shipment_status":
                            if (selectedStatus == that.STATE_READY || selectedStatus == that.STATE_CART) {
                                return that.STATE_NOT_SHIPPED;
                            } else {
                                return selectedStatus;
                            }
            }
        },

        save: function (dt) {
            var that = this;
            var data;
            dt.template = 'orderStatus';
            data = {
                order_id: orderId,
                value: dt.selectedStatus
            };
            that.loading = true;
            this.$http.post('/admin/api2/order/update-state/' + dt.statusType, data)
                .then(response => {
                    that.$emit('success-message', "Order status " + dt.statusType + " has been successfully updated.");
                    EventBus.$emit('sendHistory', {
                        message: makeHistoryMessage(dt)
                    });
                    that.orderStatuses();
                }).catch(error => {
                    that.loading = false;
                });
        },

        setSuccessMessage: function (message) {
            this.successMessage = message;
        },

        saveStatus: function (product) {
            console.log(product)
        },

        editStatus: function (product) {
            console.log(product)
        }
    },

};