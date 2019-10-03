var ThanksPage = new Vue ({
    el: '#thank-you-page',
    data: function () {
        return {
            isAuth: false
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$emit('checkLogin');
        EventBus.$on('isAuth', function (isAuth) {
            that.isAuth = isAuth;
        });
    },
    methods: {
        openLogin: function () {
            $('.user-login').popup('show');
        }
    }
})