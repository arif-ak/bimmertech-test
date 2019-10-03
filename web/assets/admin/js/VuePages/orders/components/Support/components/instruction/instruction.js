Instruction = {
    props: ['value', 'i'],
    data: function () {
        return {
            show: false,
            input: this.value
        }
    },
    watch: {
        value: function () {
            this.input = this.value
        }
    },
    methods: {
        showInput: function () {
            this.show = true;
            setTimeout(() => {
                $('#instruction_input_'+this.i).focus();
            }, 100);
        },
        returnData: function () {
            this.show = false;
            this.input = this.value
        },
        updateValue: function () {
            this.$emit('input', this.input);
            this.show = false;
        }
    },
    computed: {
        filteredInstructionName: function () {
            var value = this.input;
            value = value.replace(/https:\/\//g, '');
            value = value.replace(/http:\/\//g, '');
            return value;
        }
    }
}