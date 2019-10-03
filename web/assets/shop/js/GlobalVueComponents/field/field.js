Vue.component('section-field', {
    props: ['value', 'label', 'customClass', 'rules', 'name', 'placeholder', 'options', 'textarea', 'vincheck', 'required', 'error', 'errorMsg', 'type', 'date', 'min', 'max', 'disabled'],
    data() {
        return {
            content: this.value,
            optionsField: false,
            idField: false,
            requiredClass: false,
            errorClass: false,
            inputType: 'text',
            validateRules: this.rules,
            inputVName: false,
            inputName: false,
            validatorFieldName: false,
            validatorField: false,
            errorText: '',
            inputPrettyName: false,
            calendar: false
        };
    },
    mounted: function () {

        if (this.options) {
            this.setOptions(this.options);
        }
        if (this.id === undefined) {
            this.idField = 'field_' + (Math.random().toString(36).substr(2, 9));
        } else {
            this.idField = this.id;
        }
        if (this.required === "" || this.required == true) {
            this.requiredClass = true;
        }
        if (this.error === "" || this.error == true) {
            //this.$validator.valdate(this.id);
            this.errorClass = this.error;
        }
        if (this.type !== undefined) {
            this.inputType = this.type;
        }

        if (this.name !== undefined) {
            this.inputName = this.name;
            this.validatorFieldName = this.name;
        } else {
            this.validatorFieldName = (this.label !== undefined) ? this.label : this.idField;
        }
        if (this.label !== undefined) {
            this.inputPrettyName = this.label;
        }
        if (this.date !== undefined) {
            this.calendar = true;
        }
        this.validatorField = this.$validator.fields.find({
            id: this.id
        });
    },
    computed: {
        errss: function () {
            if (!!this.errors) {
                return this.errors.first(this.name);
            }
        },
        errorMessage: function () {
            if (!!this.errors) {
                return this.errors.first(this.name);
            }
        },
        classObj: function () {
            classObject = checker({
                'required': this.requiredClass,
                'error': this.errorClass,
                'textarea': this.textarea,
                'disabled': this.disabled
            });
            if (typeof this.customClass == 'string') {
                classObject.add(this.customClass);
            }
            return Array.from(classObject);
        },
        classObjCustom: function () {
            classObject = checker({
                'required': this.requiredClass,
                'error': (this.errorClass || this.errorMessage),
                'textarea': this.textarea,
                'disabled': this.disabled
            });
            if (typeof this.customClass == 'string') {
                classObject.add(this.customClass);
            }
            return Array.from(classObject);
        }
    },
    watch: {
        validatorFieldName: function (newVal, oldVal) {
            this.validatorField = this.$validator.fields.find({
                id: this.id
            });
            if (this.validatorField) {
                this.validatorField.update({
                    name: convertToSnakeCase(newVal)
                });
            }
            if (this.rules) {
                this.validatorField.update({
                    rules: this.rules
                });
            }
        },
        value: function (newVal, oldVal) {
            this.content = newVal;
            this.updateSelect();
        },
        error: function (newVal, oldVal) {
            if (!!newVal && newVal == true) {
                this.$validator.validate().then(valid => {
                    if (!valid) {
                        this.errorClass = this.errors.first(this.name);
                    }
                });
            } else {
                this.errorClass = newVal;
            }
        },
        errorMsg: function (newVal, oldVal) {
            if (this.errorMessage) {
                this.errorText = this.errorMessage;
            } else {
                this.errorClass = newVal;
                this.errorText = newVal;
            }
        },
        options: {
            handler(newVal) {
                if (newVal) {
                    this.setOptions(newVal);
                }
                this.updateSelect();
            },
            deep: true
        },
        content: function () {
            this.$emit('input', this.content)
            this.$emit('change');
        }
    },
    methods: {
        setOptions: function (val) {
            if (this.vincheck === '' || this.vincheck === true) {
                var newVal = [];
                for (let i = 0; i < val.length; i++) {
                    newVal.push({
                        val: val[i].model,
                        label: val[i].name
                    })
                }
                this.optionsField = newVal;
            } else {
                this.optionsField = val
            }
            this.updateSelect();
        },
        updateSelect: function () {
            var that = this;
            setTimeout(() => {
                $('#' + that.idField).dropdown({
                    useLabels: true
                });
                if (that.content === '') {
                    $('#' + that.idField).dropdown('clear')
                } else {
                    $('#' + that.idField).dropdown('set selected', that.content);
                }
            }, 25);
        },
        handleInput(e) {
            this.content = e.target.value;
        },
    }
})
Vue.component('section-button', {
    props: {
        label: {
            type: [String, Number],
            default: 'Button'
        }
    }
})