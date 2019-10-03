var Products = {
    props: ['productsData', 'access'],
    data: function () {
        return {
            loading: true,
            successMessage: false,
            //productList: []
        }
    },
    mounted: function () {
        this.productsData
    },
    updated: function () {
        if (Object.keys(this.productsData).length > 0) {
            this.loading = false;
        }
    },
    watch: {
        value: function (newVal, oldVal) {
            if (newVal) {
                this.productsData = newVal;
            }
        }
    },
    methods: {
        changeRadio: function (data) {
            this.$emit('change', data)
        },
        loadListOfWarehouse: function () {
            var that = this;
            this.$http.get('/admin/api2/warehouse-list')
                .then(response => {
                    that.warehousesList = response.data.data;
                }).catch(error => {

                })
        }
    },
    computed: {

    }
};