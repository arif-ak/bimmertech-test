CategoryList = {
    data: function () {
        return {
            links: []
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$on('CategoryList', function (data) {
            that.links = data;
        })
    }
}