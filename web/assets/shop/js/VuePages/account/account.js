var AccountPage = new Vue({
    el: '#account',
    data: function () {
        return {
            tab: 'orders'
        }
    },
    created: function () {
        let _this = this;
        if (window.location.hash) {
            _this.tab = window.location.hash.slice(1);
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$on('isAuth', function (isAuth) {
            if (!isAuth && that.tab != 'close') {
                window.location.href = '/';
            }
        });
        window.history.replaceState(this.tab, "Title", window.location.pathname + '#' + this.tab);
        window.onpopstate = function (event) {
            that.tab = event.state;
        };
    },
    methods: {
        changeTab: function (name) {
            window.history.pushState(name, name, window.location.pathname + '#' + name);
            this.tab = name;
        },
    },
    components: {
        'section-orders': AccountOrders,
        'section-instruction': AccountInstruction,
        'section-password': AccountPassword,
        'section-close': AccountClose,
        'section-info': AccountInfo
    }
});