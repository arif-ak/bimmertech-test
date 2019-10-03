function addTagP (text) {
    return "<p>"+text+"</p>"
}
var description = new Vue({
    el: '#customPage',
    data: function () {
        return {
            pageContent: '<p></p>',
            loading: true
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$on('documentReady', function () {
            that.loading = false;
            that.pageContent = addTagP(pageContent);
        });
    }
});
