var CartCode = {
    props: ['value'],
    data () {
        return {
            content: this.value
        }
    },
    watch: { 
        value: function(newVal, oldVal) {
            this.content = newVal;
        }
    },
    methods: {
        handleInput (e) {
            this.content = e.target.value;
            this.$emit('input', this.content)
        },
        sendCode: function () {
            this.$emit('click');
        }
    }
}