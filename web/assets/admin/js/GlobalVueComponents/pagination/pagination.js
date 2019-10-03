Vue.component('section-pagination', {
    props: {
        value: {
            type: [String, Number],
            default: 1
        },
        pages: {
            type: Number,
            default: 1
        }
    },
    methods: {
        update: function (value) {
            if (value>0 && value<=this.pages) {
                this.$emit('input', value);
            }
        }
    }
})