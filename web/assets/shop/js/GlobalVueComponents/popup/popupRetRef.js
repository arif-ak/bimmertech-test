Vue.component('popup-ret-ref', {
    props: ['value'],
    template: `
    <span @click="showPopup($event)" class="bt-icons-wrapper">
        <span class="bt-returned bt-icon" v-if="hasReturn"></span>
    </span>
    `,
    data() {
        return {
            productData: false,
            hasReturn: false
        }
    },
    mounted: function () {
        if (this.value) {
            this.hasRetRef(this.value);
            this.productData = this.value;
        }
    },
    watch: {
        value: function (newVal, oldVal) {
            if (newVal) {
                this.hasRetRef(newVal);
                this.productData = newVal;
            }
        }
    },
    methods: {
        showPopup: function (data) {
            if (this.hasReturn) {
                $(data.target)
                    .btpopup({
                        className: {
                            loading: 'loading',
                            btpopup: 'cui btpopup user ret-ref',
                            position: 'top center',
                            visible: 'visible'
                        },
                        html: '<div class="header grid-x">Returned</div>',
                        // boundary: '#Orders',
                        position: 'top center',
                        // on: 'click',
                        hideOnScroll: true,
                        // jitter: 10,
                        arrowPixelsFromEdge: 30,
                        // distanceAway: -5,
                    }).btpopup('show');
            }
        },
        hasRetRef: function (product) {
            this.hasReturn = product.is_returned;
        }


    }
});