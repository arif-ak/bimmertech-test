var Logistic = {
    props: ['access'],
    data: function () {
        return {
            warehouses: [],
            loading: false,
            successMessage: false,
            warehousesList: []
        }
    },
    components: {
        'section-warehouse': Warehouse,
        'section-change-warehouse': changeWarehouse,
        'section-base-modal': baseModal,
        'section-form': Form
    },
    mounted: function () {
        this.loadWarehouses();
        this.loadListOfWarehouse();
        EventBus.$on("reloadData", data => {
            if (data == 'loadWarehouses' || data == 'all') {
                this.loadWarehouses();
            }
        });
    },
    methods: {
        loadWarehouses: function () {
            var that = this;
            this.loading = true;
            this.$http.get('/admin/api2/order/' + orderId + '/warehouse-order-items')
                .then(response => {
                    that.warehouses = response.data.data;
                }).catch(error => {

                }).finally(function () {
                    that.loading = false;
                });
        },
        changeRadio: function (data) {
            this.$emit('change', data);
        },

        loadListOfWarehouse: function () {
            var that = this;
            this.$http.get('/admin/api2/warehouse-list')
                .then(response => {
                    that.warehousesList = response.data.data;
                }).catch(error => {});
        }
    },
    computed: {
        filteredWarehouses: function () {
            var filteredWarehouses = [];
            for (const key in this.warehouses) {
                if (this.warehouses.hasOwnProperty(key)) {
                    filteredWarehouses.push({
                        name: key,
                        products: this.warehouses[key].products,
                        vin: this.warehouses[key].vin,
                        shipments: this.warehouses[key].shipments,
                        usb_coding_board_access: this.warehouses[key].usb_coding_board_access,
                        logistic_board_access: this.warehouses[key].logistic_board_access,
                        order_item_units: this.warehouses[key].order_item_units,
                        product_usb_coding: this.warehouses[key].product_usb_coding,
                        order_item_usb_coding_free: this.warehouses[key].order_item_usb_coding_free,
                        order_item_usb_coding_sent: this.warehouses[key].order_item_usb_coding_sent,
                        shippingMethods: this.warehouses[key].shippingMethods,
                        id: this.warehouses[key].warehouse.id
                    });
                }
            }
            return filteredWarehouses;
        },
        filteredWarehousesList: function () {
            var warehousesList = [];
            for (let i = 0; i < this.warehousesList.length; i++) {
                warehousesList.push({
                    label: this.warehousesList[i].name,
                    val: this.warehousesList[i].id
                });
            }
            return warehousesList;
        }
    }
};