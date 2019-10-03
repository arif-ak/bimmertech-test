function addTagP(text) {
    return "<p>" + text + "</p>"
}
var description = new Vue({
    el: '#description',
    data: function () {
        return {
            productDescription: '',
            loading: true
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$on('documentReady', function () {
            that.loading = false;
            that.productDescription = productDescription;
        });
    }
});
//"undefined" != typeof module && module.exports && (module.exports = VueEasyTinyMCE);

var installers = new Vue({
    el: '#installers',
    data: function () {
        return {
            productInstaller: '',
            loading: true
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$on('documentReady', function () {
            that.loading = false;
            that.productInstaller = productInstaller;
        });
    }
});
