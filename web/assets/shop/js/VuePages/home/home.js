var HomePage = new Vue({
    el: '#HomePage',
    components: {
        'section-category-list': CategoryList
    },
    data: function () {
        return {
            posts: [],
            recomended: []
        }
    },
    created: function () {
        var that = this;
        EventBus.$on('documentReady', function (data) {
            if (data.loadBlogOnHomePage) {
              that.loadBlog();
            }
            if (data.loadRecomendedOnHomePage) {
                EventBus.$on('updateCompatibility', function (vincheck) {
                    that.loadRecomended();
                });
            }
        })
    },
    methods: {
        loadBlog: function () {
            this.$http.get('/api2/blog/most-reviewed')
            .then(response => {
                this.posts = response.data.data;
            }).catch(error => {
              // this.reviewsLoader = false;
            }).finally(() => {
              // this.loadMoreBtn = this.checkLoadMoreBtn(this.productList.length);
            });
        },
        loadRecomended: function () {
            this.$http.get('/api2/home/recommended')
            .then(response => {
                this.recomended = response.data;
            }).catch(error => {
              // this.reviewsLoader = false;
            }).finally(() => {
              // this.loadMoreBtn = this.checkLoadMoreBtn(this.productList.length);
            });
        }
    }
})