var Blogs = new Vue({
    el: '#blogs',
    data: function () {
        return {
            // categories: {"Trending":[{"id":5,"title":"sed","category_name":"Trending","date":"2018-11-09 15:11:21","enable":true,"slug":"sed","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/55/aa/4070d728583849a43ec5f536c13d.jpeg","oneToOne":"media/image/f1/c0/bde44873e3ac28b1f404b309e196.png"}},{"id":3,"title":"qsdfgjk,mhaasnm  adfgm,","category_name":"Trending","date":"2018-11-09 15:11:26","enable":true,"slug":"Sed_ut_perspiciatis_unde","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/1f/88/2fdd736bb46e209fc371f3e2369a.png","oneToOne":"media/image/4c/47/717f40e0c2e563bf7379a462f18c.png"}},{"id":4,"title":"sdfgnmnbx xsfvgnbf","category_name":"Trending","date":"2018-11-09 15:11:03","enable":true,"slug":"Sed_ut_perspiciatis_unde23","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/12/91/2043bd8fed68079d7f190d600a1c.png","oneToOne":"media/image/7a/37/5b4837bddbdae070d5409cc70950.png"}},{"id":3,"title":"qsdfgjk,mhaasnm  adfgm,","category_name":"Trending","date":"2018-11-09 15:11:26","enable":true,"slug":"Sed_ut_perspiciatis_unde","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/1f/88/2fdd736bb46e209fc371f3e2369a.png","oneToOne":"media/image/4c/47/717f40e0c2e563bf7379a462f18c.png"}},{"id":3,"title":"qsdfgjk,mhaasnm  adfgm,","category_name":"Trending","date":"2018-11-09 15:11:26","enable":true,"slug":"Sed_ut_perspiciatis_unde","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/1f/88/2fdd736bb46e209fc371f3e2369a.png","oneToOne":"media/image/4c/47/717f40e0c2e563bf7379a462f18c.png"}},{"id":5,"title":"sed","category_name":"Trending","date":"2018-11-09 15:11:21","enable":true,"slug":"sed","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/55/aa/4070d728583849a43ec5f536c13d.jpeg","oneToOne":"media/image/f1/c0/bde44873e3ac28b1f404b309e196.png"}}]},
            // latestPosts: [{"id":5,"title":"sed","category_name":"Trending","date":"2018-11-09 15:11:21","enable":true,"slug":"sed","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/55/aa/4070d728583849a43ec5f536c13d.jpeg","oneToOne":"media/image/f1/c0/bde44873e3ac28b1f404b309e196.png"}},{"id":4,"title":"sdfgnmnbx xsfvgnbf","category_name":"Trending","date":"2018-11-09 15:11:03","enable":true,"slug":"Sed_ut_perspiciatis_unde23","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/12/91/2043bd8fed68079d7f190d600a1c.png","oneToOne":"media/image/7a/37/5b4837bddbdae070d5409cc70950.png"}},{"id":3,"title":"qsdfgjk,mhaasnm  adfgm,","category_name":"Trending","date":"2018-11-09 15:11:26","enable":true,"slug":"Sed_ut_perspiciatis_unde","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/1f/88/2fdd736bb46e209fc371f3e2369a.png","oneToOne":"media/image/4c/47/717f40e0c2e563bf7379a462f18c.png"}},{"id":4,"title":"sdfgnmnbx xsfvgnbf","category_name":"Trending","date":"2018-11-09 15:11:03","enable":true,"slug":"Sed_ut_perspiciatis_unde23","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/12/91/2043bd8fed68079d7f190d600a1c.png","oneToOne":"media/image/7a/37/5b4837bddbdae070d5409cc70950.png"}},{"id":4,"title":"sdfgnmnbx xsfvgnbf","category_name":"Trending","date":"2018-11-09 15:11:03","enable":true,"slug":"Sed_ut_perspiciatis_unde23","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/12/91/2043bd8fed68079d7f190d600a1c.png","oneToOne":"media/image/7a/37/5b4837bddbdae070d5409cc70950.png"}},{"id":5,"title":"sed","category_name":"Trending","date":"2018-11-09 15:11:21","enable":true,"slug":"sed","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/55/aa/4070d728583849a43ec5f536c13d.jpeg","oneToOne":"media/image/f1/c0/bde44873e3ac28b1f404b309e196.png"}},{"id":5,"title":"sed","category_name":"Trending","date":"2018-11-09 15:11:21","enable":true,"slug":"sed","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/55/aa/4070d728583849a43ec5f536c13d.jpeg","oneToOne":"media/image/f1/c0/bde44873e3ac28b1f404b309e196.png"}},{"id":4,"title":"sdfgnmnbx xsfvgnbf","category_name":"Trending","date":"2018-11-09 15:11:03","enable":true,"slug":"Sed_ut_perspiciatis_unde23","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/12/91/2043bd8fed68079d7f190d600a1c.png","oneToOne":"media/image/7a/37/5b4837bddbdae070d5409cc70950.png"}},{"id":4,"title":"sdfgnmnbx xsfvgnbf","category_name":"Trending","date":"2018-11-09 15:11:03","enable":true,"slug":"Sed_ut_perspiciatis_unde23","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/12/91/2043bd8fed68079d7f190d600a1c.png","oneToOne":"media/image/7a/37/5b4837bddbdae070d5409cc70950.png"}},{"id":4,"title":"sdfgnmnbx xsfvgnbf","category_name":"Trending","date":"2018-11-09 15:11:03","enable":true,"slug":"Sed_ut_perspiciatis_unde23","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/12/91/2043bd8fed68079d7f190d600a1c.png","oneToOne":"media/image/7a/37/5b4837bddbdae070d5409cc70950.png"}},{"id":4,"title":"sdfgnmnbx xsfvgnbf","category_name":"Trending","date":"2018-11-09 15:11:03","enable":true,"slug":"Sed_ut_perspiciatis_unde23","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/12/91/2043bd8fed68079d7f190d600a1c.png","oneToOne":"media/image/7a/37/5b4837bddbdae070d5409cc70950.png"}},{"id":5,"title":"sed","category_name":"Trending","date":"2018-11-09 15:11:21","enable":true,"slug":"sed","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/55/aa/4070d728583849a43ec5f536c13d.jpeg","oneToOne":"media/image/f1/c0/bde44873e3ac28b1f404b309e196.png"}},{"id":5,"title":"sed","category_name":"Trending","date":"2018-11-09 15:11:21","enable":true,"slug":"sed","header":"Sed ut perspiciatis","sub_header":"Sed ut perspiciatis","thumbnail":{"twoToOne":"media/image/55/aa/4070d728583849a43ec5f536c13d.jpeg","oneToOne":"media/image/f1/c0/bde44873e3ac28b1f404b309e196.png"}}],
            loadMore: false,
            latestPosts: [],
            categories: null,
            recomended: []
        }
    },
    mounted: function () {
        var that = this;
        this.loadPosts();
        EventBus.$on('documentReady', function (data) {
            if (data.loadRecomendedOnBlogListPage) {
                EventBus.$on('updateCompatibility', function (vincheck) {
                    that.loadRecomended();
                });
            }
        })
    },
    methods: {
        loadPosts: function () {
            var that = this;
            getRequest('blog')
            .then(response => {
                that.categories = response.data.postByCategory;
                that.latestPosts = response.data.latest;
                that.filterCategories();
                console.log(that.filteredCategories);
                setTimeout(() => {
                    console.log(that.filteredCategories);
                }, 5000);
            }).catch(error => {
                // this.userLikesList = [];
            });
        },
        loadRecomended: function () {
            this.$http.get('/api2/blog-interesting-in')
            .then(response => {
                this.recomended = response.data;
            }).catch(error => {
              // this.reviewsLoader = false;
            }).finally(() => {
              // this.loadMoreBtn = this.checkLoadMoreBtn(this.productList.length);
            });
        }
    },
    computed: {
        filteredCategories: function () {
            var filteredCategories = [];
            var l = 0
            for (const key in this.categories) {
                if (this.categories.hasOwnProperty(key)) {
                    filteredCategories.push({
                        label: key,
                        data: []
                    });
                    for (let i = 0; i < this.categories[key].length; i+=3) {
                        var articles = []
                        for (let j = i; j < i+3; j++) {
                            if (this.categories[key][j]) {
                                articles.push(this.categories[key][j]);
                            }
                        }
                        filteredCategories[l].data.push(articles);
                    }
                }
                l++;
            }
            return filteredCategories;
        },
        filteredLatestPosts: function () {
            var filteredLatestPosts = [];
            for (let i = 0; i < this.latestPosts.length; i+=3) {
                if (!this.loadMore && i>=9) {
                    break;
                }
                var articles = []
                for (let j = i; j < i+3; j++) {
                    if (this.latestPosts[j]) {
                        articles.push(this.latestPosts[j]);
                    }
                }
                filteredLatestPosts.push(articles);
            }
            return filteredLatestPosts;
        }
    }
})

