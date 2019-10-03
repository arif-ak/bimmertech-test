Vue.component('section-radio', {
    props: ['value', 'label', 'items'],
    data () {
        return {
            content: this.value
        }
    },
    mounted: function () {
        $('.bt.checkbox').checkbox();
    },
    watch: { 
        value: function(newVal, oldVal) {
            this.content = newVal;
        }
    },
    methods: {
        changeSelect: function () {
            this.$emit('input', this.content)
            this.$emit('change');
        }
    }
})