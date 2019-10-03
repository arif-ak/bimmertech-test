Vue.component('section-compatibility-icon', {
    props: ['value'],
    data() {
        return {
            compatibility: null
        }
    },
    mounted: function () {
        if (this.value !== undefined) {
            this.compatibility = this.value;
        }
    },
    watch: {
        value: function (newVal, oldVal) {
            if (newVal !== undefined) {
                this.compatibility = newVal;
            }
        },
    },
    computed: {
        className: function () {
            if (this.compatibility === null) {
                return {
                    'bt-attention': true
                }
            } else if (this.compatibility === 'Yes') {
                return {
                    'bt-yes': true
                }
            } else if (this.compatibility === 'No') {
                return {
                    'bt-no': true
                }
            } else if (this.compatibility === 'Not sure') {
                return {
                    'bt-maybe': true
                }
            }
        }
    }
})