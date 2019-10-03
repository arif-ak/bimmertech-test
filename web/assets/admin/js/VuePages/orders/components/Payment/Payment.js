var Payment = {
    props: ['refunds', 'paymentItems', 'balanceMap', 'orderBalance', 'access'],
    data: function () {
        return {
            modalActive: false,
            loading: true,
        }
    },
    components: {
        'section-payment-modal': PaymentModal,
    },
    updated: function () {
        if (Object.keys(this.paymentItems).length > 0) {
            this.loading = false;
        }
    },
    mounted: function () {

    },
    methods: {
        refundsModal: function (data) {
            EventBus.$emit("refundsModal", data);
        },
        changeHandle: function (data) {
            this.$emit('change', data);
        }
    }
};