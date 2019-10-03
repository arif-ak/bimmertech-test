Vue.component('section-compatibility-text', {
    props: ['value', 'model'],
    data () {
        return {
            compatibility: null
        }
    },
    mounted: function () {
        var that = this;
        if (this.value !== undefined) {
            this.compatibility = this.value;
        }
    },
    watch: { 
        value: function(newVal, oldVal) {
            if (newVal !== undefined) {
                this.compatibility = newVal;
            }
        }
    },
    methods: {
        openVincheck: function () {
            $('#section-vincheck').popup('show');
        },
    }
})