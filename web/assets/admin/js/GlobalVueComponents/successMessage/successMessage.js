Vue.component('section-success-message', {
    props: {
        message: {
            type: [String, Number],
            default: "Successfully updated"
        }
    },
    methods: {
        close: function () {
            this.$emit('close');
        }
    }
})