var Coding = {
    props: ['access'],
    data: function () {
        return {
            products: [],
            loading: false,
            successMessage: false,
        }
    },

    components: {
        'section-product': CodingProduct,
        'section-action': CodingAction,
    },

    mounted: function () {
        this.loadCodingItems();
        EventBus.$on("reloadData", data => {
            if (data == 'loadCodingItems' || data == 'all') {
                this.loadCodingItems();
            }
        });
    },

    methods: {
        loadCodingItems: function () {
            var that = this;
            this.loading = true;
            this.$http.get('/admin/api2/order/' + orderId + '/codding-board')
                .then(response => {
                    that.products = that.filterCodingResult(response.data.data);
                }).catch(error => {

                }).finally(function () {
                    that.loading = false;
                });
        },

        filterCodingResult: function (products) {
            var listOfProducts = [];

            if (products.order_item) {
                for (let i = 0; i < products.order_item.length; i++) {
                    console.log();

                    let _class = 'yellow',
                        _btnText = 'Modify',
                        _text = 'coded';
                    if (products.order_item[i].coding.status != 'completed') {
                        products.order_item[i].date = null;
                        _class = 'green';
                        _btnText = 'Code';
                        _text = '';
                    }
                    if (products.order_item[i].order_item_unit_return.length) {
                        _class += ' disabled';
                    }
                    Object.assign(products.order_item[i].coding, {
                        statusClass: _class,
                        statusBtnText: _btnText,
                        statusText: _text,
                    });
                    listOfProducts.push(products.order_item[i]);
                }
            }

            return listOfProducts;
        },
        setSuccessMessage: function (message) {
            this.successMessage = message;
        },
        changeStatus: function (product) {
            this.loading = true;
            let data = {
                "order_item_id": product.id,
                "status": product.coding.status != 'completed' ? 'completed' : 'not coded'
            };
            this.$http.post('/admin/api2/order/' + orderId + '/edit-codding', data)
                .then(response => {
                    if (response) {
                        EventBus.$emit("sendHistory", {
                            type: 'coding',
                            message: (data.status != 'completed') ? `Edited status to \'Not coded\' for coding products: ${product.name}` : `\'Coded\' status for products: ${product.name}`
                        });
                        EventBus.$emit("reloadData", 'loadCodingItems');
                    }
                }).catch(error => {
                    console.log(error.data);
                }).finally(function () {
                    this.loading = false;
                });
        }
    },
};