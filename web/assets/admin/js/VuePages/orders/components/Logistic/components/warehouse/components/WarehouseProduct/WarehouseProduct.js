var WarehouseProduct = {
    props: ['product', 'vin', 'access'],
    data: function () {
        return {
            componentRadio: false,
            edit: true,
        }
    },
    mounted: function () {
        if (this.product.drop_down.length > 0 && this.product.savePrice !== null) {
            return this.product.savePrice.title = this.product.savePrice.title.concat(', ', '');
        }
    }
};