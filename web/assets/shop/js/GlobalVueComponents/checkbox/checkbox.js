Vue.component('section-checkbox', {
    props: {
        id: [String, Number],
        checked:[Array, Boolean],
        value: {
            type: [String, Number, Boolean],
            default: false
        },
        error: {
            type: Boolean,
            default: false
        },
        addon: {
            default: false
        }
    },
    model: {
        prop: 'checked',
        event: 'change'
    },
    data () {
        return {
            checkboxId: '',
            selected: [],
            errorClass: false
        }
    },
    mounted: function () {
        if (this.id === undefined) {
            this.checkboxId = 'checkbox_'+(Math.random().toString(36).substr(2, 9));
        } else {
            this.checkboxId = this.id;
        }
        if (this.error==="" || this.error==true) {
            this.errorClass=true;
        }
        this.selected = this.checked;
        this.checkbox();
    },
    watch: {
        checked: {
            handler(newVal){
                this.selected = newVal;
            },
            deep: true
        },
        error: function(newVal, oldVal) {
            this.errorClass = newVal;
        },
    },
    methods: {
        handleInput (val) {
            this.$emit('input', this.selected);
            this.$emit('change', this.selected);
        },
        checkbox: function () {
            var that = this;
            setTimeout(() => {
                $('#'+that.checkboxId).checkbox();
            }, 500);
        }
    }
})