var Menu = {
    props: {
        value: {
            type: Array,
            default: Array
        },
    },
    data: function () {
        return {
            content: [],
        }
    },
    watch: {
        value: function () {
            this.content = this.value
        }
    },
    updated: function () {
        this.$emit('input', this.content)
    },
    methods: {
        deleteBlock: function (i) {
            this.content.splice(i,1);
            this.$emit('change')
        }
    }
}