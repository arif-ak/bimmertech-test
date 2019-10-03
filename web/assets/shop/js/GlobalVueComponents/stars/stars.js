Vue.component('section-stars', {
    props: ['value', 'disabled', 'size', 'id', 'max'],
    data () {
        return {
            content: 0,
            maxStars: 5,
            idStars: false,
            disabledStars: false
        }
    },
    mounted: function () {
        if (this.id === undefined) {
            this.idStars = 'stars_'+(Math.random().toString(36).substr(2, 9));
        } else {
            this.idStars = this.id;
        }
        if (this.value>=0) {
            this.content = Math.round(parseFloat(this.value));
        }
        if (!this.max) {
            this.maxStars = 5;
        } else {
            this.maxStars = this.max;
        }
        if (this.disabled=="" || this.disabled==true) {
            this.disabledStars = true;
        } else {
            this.disabledStars = false;
        }
        setTimeout(() => {
            this.geterateRating()
        }, 400);
    },
    watch: { 
        value: function(newVal, oldVal) {
            if (newVal>=0) {
                this.content = Math.round(parseFloat(newVal));
            }
            this.geterateRating();
        },
        max: function (newVal, oldVal) {
            if (!this.max) {
                this.maxStars = 5;
            } else {
                this.maxStars = this.max;
            }
            this.geterateRating();
        },
        disabled: function (newVal, oldVal) {
            if (newVal) {
                if (newVal=="" || newVal==true) {
                    this.disabledStars = true;
                } else {
                    this.disabledStars = false;
                }
                this.geterateRating();
            }
        }
    },
    methods: {
        handleInput () {
            if (!this.disabledStars) {
                setTimeout(() => {
                    this.content = $('#'+this.idStars).rating('get rating');
                    this.geterateRating();
                    this.$emit('input', this.content);
                    this.$emit('click');
                }, 20);
            } else {
                this.$emit('click');
            }

        },
        geterateRating: function () {
            setTimeout(() => {
                $('#'+this.idStars).rating();
                if (this.disabledStars) {
                    $('#'+this.idStars).rating('disable');
                }
            }, 10);
        }
    }
})