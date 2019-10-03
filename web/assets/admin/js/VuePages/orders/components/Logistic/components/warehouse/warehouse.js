var Warehouse = {
    props: ['warehouse', 'warehousesList', 'i', 'access'],
    components: {
        'section-product': WarehouseProduct,
        'section-product-usb-coding': WarehouseProductUsbCoding
    },
    data: function () {
        return {
            radio: false
        }
    },
    watch: {
        warehouse: {
            handler(newVal) {
                this.radio = false;
            },
            deep: true
        },
    },
    methods: {
        shipment: function (items, option) {
            var that = this;
            EventBus.$emit("logisticShipmentWarehouse", {
                create: option,
                header: 'Prepare label',
                id: that.warehouse.id,
                shippingMethods: that.warehouse.shippingMethods,
                order_item_units: items,
                template: 'PrepareLabel',
            });
        },
        editTrackingNumber: function (items, option) {
            var that = this;
            EventBus.$emit("baseModal", {
                header: 'Edit tracking number',
                action: option ? '' : 'update-order-shipment',
                items: items,
                template: 'EditTrackingNumber'
            });
        },
        sendViaEmail: function (items, option) {
            EventBus.$emit("baseModal", {
                header: option ? 'Edit sent USB coding' : 'Send via email',
                action: option ? 'order/usb-coding-update' : 'order/usb-coding-create',
                items: items,
                template: 'SendViaEmail'
            });
        },
        changeWarehouse: function (items, option) {
            var that = this;
            EventBus.$emit("changeWarehouse", {
                id: that.warehouse.id,
                items: items,
                action: option ? 'order/change-usb-coding-warehouse' : 'order/change-warehouse',
                warehousesList: that.warehousesList,
            });
        }
    },
    computed: {
        quantityLength: function () {
            var count = 0;
            for (let i = 0; i < this.warehouse.products.length; i++) {
                count += this.warehouse.products[i].quantity
            }
            return count;
        }
    }
};