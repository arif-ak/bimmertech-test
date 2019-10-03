Vue.component('section-price', {
    props: ['value'],
    data: function () {
        return {
            price: 0
        }
    },
    mounted: function () {
        if (this.value) {
            this.price = this.priceMacro(this.value);
        }
    },
    watch: {
        value: function (newVal, oldVal) {
            if (newVal) {
                this.price = this.priceMacro(newVal);
            }
        }
    },
    methods: {
        priceMacro: function (data) {
            return (data / 100).format(2, 3);
        },
    }
})