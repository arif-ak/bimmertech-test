var searchPage = new Vue({
    el: '#searchPage',
    data: function () {
        return {
            posts: [],
            productList: [],

            loadMorePosts: false,
            loadMoreProducts: false,
            loading: true,
            vincheck: ''
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$on('updateCompatibility', function (vincheck) {
            that.vincheck = vincheck;
            that.loadResults();
        });
    },
    methods: {
        loadResults: function () {
            searchText = searchText.replace(/&quot;/g, '"');
            this.$http.get('/api2/search?q=' + searchText)
                .then(response => {
                    this.posts = response.data.items.posts;
                    this.productList = this.parseFun(response.data.items.products.products, response.data.items.products.productContainers);
                    this.loading = false;
                }).catch(error => {
                    // this.reviewsLoader = false;
                }).finally(() => {
                    // this.loadMoreBtn = this.checkLoadMoreBtn(this.productList.length);
                    this.loading = false;
                });
        },
        parseFun(products, productsContainer) {
            var container = []
            for (const key in productsContainer) {
                if (productsContainer.hasOwnProperty(key)) {
                    container.push(productsContainer[key]);
                }
            }
            for (const key in products) {
                if (products.hasOwnProperty(key)) {
                    container.push(products[key]);
                }
            }
            return container;
        },
    },
    computed: {
        filteredPosts: function () {
            var Posts = [];
            for (let i = 0; i < this.posts.length; i += 3) {
                if (!this.loadMorePosts && i >= 9) {
                    break;
                }
                var articles = []
                for (let j = i; j < i + 3; j++) {
                    if (this.posts[j]) {
                        articles.push(this.posts[j]);
                    }
                }
                Posts.push(articles);
            }
            return Posts;
        },
        filteredProductList: function () {
            var Products = [];
            for (let i = 0; i < this.productList.length; i++) {
                Products.push(this.productList[i]);
                if (!this.loadMoreProducts && i == 7) {
                    return Products;
                }
            }
            return Products;
        }
    }
})