Vue.component('section-product-list', {
    props: ['value'],
    data () {
        return {
            productList: []
        }
    },
    mounted: function () {
        this.productList = this.value
    },
    watch: { 
        value: {
            handler(newVal){
                this.productList = newVal;
            },
            deep: true
        }
    }
})