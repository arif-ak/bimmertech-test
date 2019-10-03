var OrdersPage = new Vue({
    el: '#orders-page',
    data: function () {
        return {
            access: {
                order_refund_edit: false,
                vin_edit: false
            },
            initialVin: '',
            vin: '',
            customerName: '',
            edit: false,
            loading: false,
            refunds: [],
            paymentItems: [],
            productsObj: [],
            orderBalance: false,
            balanceMap: [],
            productMap: [],
            boardData: {
                products: false,
                notes: false,
                logistic: false,
                support: false,
                coding: false,
            }
        };
    },
    components: {
        'section-statuses': Status,
        'section-payment': Payment,
        'section-notes': Notes,
        'section-product-board': Products,
        'section-logistic-board': Logistic,
        'section-support-board': Support,
        'section-coding-board': Coding,
    },
    mounted: function () {
        let _self = this;
        this.initialVin = vin;
        this.vin = vin;
        this.customerName = customer;
        this.loadBoardAccess();
        this.loadNotes();
        this.loadProducts();
        this.getBalance();
        this.getRetRefInfo();
        EventBus.$on("reloadData", data => {
            if (data == 'all') {
                this.getBalance();
                this.getRetRefInfo();
                this.loadProducts();
            }
        });
        EventBus.$on("updateVin", data => {
            this.vin = data.vin;
        });
        EventBus.$on("sendHistory", data => this.sendHistory(data));
    },
    methods: {
        loadBoardAccess: function () {
            var that = this;
            this.loading = true;
            this.$http.get('/admin/api2/order/board-access')
                .then(response => {
                    that.access = response.data.data;
                }).catch(error => {});
        },
        getBalance: function () {
            postRequest(`order/balance`, {
                    order_id: orderId
                })
                .then(response => {
                    if (response.data) {
                        this.parseBalance(response.data);
                    }
                    this.loading = false;
                }).catch(error => {});

        },
        parseBalance: function (data) {
            let _this = this;
            for (const j in data) {
                if (data.hasOwnProperty(j)) {
                    switch (j) {
                        case 'order_item_unit_balance':
                            let map = new Map(),
                                d = data[j];
                            _this.paymentItems = d;
                            for (const i in d) {
                                if (d.hasOwnProperty(i)) {
                                    map.set(d[i].id, d[i]);
                                }
                            }
                            _this.balanceMap = map;
                            break;
                        case 'order_balance':
                            _this.orderBalance = data[j];
                            break;
                        default:
                            break;
                    }
                }
            }
        },
        filterReturn: function (data) {
            let map = new Map();
            for (const i in data) {
                if (data.hasOwnProperty(i)) {
                    map.set(data[i].id, data[i]);
                }
            }
            return map;
        },
        loadNotes: function () {
            getRequest(`order/${orderId}/order-note`)
                .then(response => {
                    this.boardData.notes = response.data;
                }).catch(error => {});
        },
        loadProducts: function () {
            getRequest(`order/${orderId}/products`)
                .then(response => {
                    this.boardData.products = response.data;
                }).catch(error => {});
        },
        getRetRefInfo: function () {
            this.loading = true;
            getRequest(`order/${orderId}/refund`)
                .then(response => {
                    this.refunds = response.data.filter(item => item.total > 0);
                }).catch(error => {});
        },
        editV: function (event) {
            event.preventDefault();
            var target = event.target;
            while (target != event.currentTarget) {
                if (this.isButton(target)) {
                    this.edit = !this.edit;
                    if (this.isEdit(target)) {
                        return;
                    }
                    if (this.isSave(target)) {
                        return postRequest('order/' + orderId + '/vin', {
                            vin: this.vin
                        }).then(response => {
                            this.sendHistory({
                                type: 'vin'
                            });
                        }).catch(error => {});
                    }
                }
                target = target.parentNode;
            }
        },
        isButton: function (element) {
            return element.classList.contains("summaryEditBtn");
        },
        isEdit: function (element) {
            return element.textContent === 'Edit';
        },
        isSave: function (element) {
            return element.textContent === 'Save';
        },

        sendHistory: function (data) {
            var message = '';
            if (data.type == 'vin') {
                message += 'Changed the VIN number from ' + this.initialVin + ' to ' + this.vin;
                this.initialVin = this.vin;
            }
            if (data.type == 'order') {
                message += 'Changed status from ' + data.from + ' to ' + data.to;
            }
            if (data.type == 'note') {
                message += 'Added Note: ' + data.message;
            }
            if (data.type == 'refund-edit') {
                message += data.message;
            }
            if (data.type == 'refund-create') {
                message += data.message;
            }
            if (data.type == 'coding') {
                message += data.message;
            }
            if (!data.type) {
                message += data.message;
            }
            postRequest(`history/order/${orderId}/created`, {
                message: message
            }).then(response => {

            }).catch(error => {});
        }
    }
});