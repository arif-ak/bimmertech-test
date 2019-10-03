var WarehouseProductUsbCoding = {
    //props: ['shipment', 'value', 'p', 'i'], 
    props: ['product', 'vin', 'warehouse', 'access'],
    data: function () {
        return {
            componentRadio: false,
            edit: true
        }
    },
    computed: {
        retRefInfo: function () {
            return (this.product.order_item_unit_return.length > 0 || product.order_item_unit_refund.length > 0);
        }
    },
    methods: {
        shipment2: function () {
            var that = this;
            EventBus.$emit("logisticShipmentWarehouse", {
                create: true,
                id: orderId,
                shippingMethods: this.warehouse.shippingMethods,
                order_item_units: this.warehouse.order_item_units
            });
        },
        changeWarehouse2: function () {
            var that = this;
            EventBus.$emit("changeWarehouse", {
                warehouse: Warehouse.warehouse,
                warehousesList: Warehouse.warehousesList
            });
        }
    },
};