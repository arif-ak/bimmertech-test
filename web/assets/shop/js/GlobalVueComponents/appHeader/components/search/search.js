var Search = {
    data: function () {
        return {
            search: '',
            results: false,
            resultsShow: false,
            messageShow: false
        }
    },
    mounted: function () {
        var that = this;
        $('.search-form').search({
            transition: 'fade',
            minCharacters: 3,
            cache: false,
            apiSettings: {
                url: '/search?q={query}'
            },
            fields: {
                results: 'items',
                products: 'products',
                category: 'category',
                posts: 'posts',
                header: 'header',
                slug: 'slug',
                description: null,
                taxon: 'taxon',
                taxonURL: 'taxon_url',
                title: 'name',
                url: 'url'
            },
            onResults: function (response) {
                if (response.items.posts.length == 0 && response.items.products.length == 0) {
                    that.resultsShow = false;
                    that.messageShow = true;
                    that.results = false;
                } else {
                    that.results = response.items;
                    // that.resultsShow = true;
                }
            },
            onResultsOpen: function () {
                that.resultsShow = true;
            },
            onResultsClose: function () {
                setTimeout(() => {
                    // console.log('close');

                    // that.resultsShow = false;
                    // that.messageShow = false;
                }, 200);
            },
        });
        if (typeof searchText != 'undefined') {
            this.search = he.decode(searchText);
        }
    },
    watch: {
        search: function () {
            if (this.search.length == 1) {
                this.search = $.trim(this.search)
            }
        }
    },
    methods: {
        focus: function (val) {
            if (val.length <= 2 && val != '') {
                this.messageShow = true;
                this.resultsShow = false;
                this.results = false;
            } else {
                this.messageShow = false;
                this.resultsShow = true;
            }
        },
        input: function (val) {
            if (val.length <= 2) {
                this.messageShow = true;
                this.resultsShow = false;
                this.results = false;
            } else {
                this.messageShow = false;
                this.resultsShow = true;
            }
        },
        onMessageClose: function () {
            var that = this;
            this.messageShow = false;
            setTimeout(() => {
                that.resultsShow = false;
            }, 200);
        },
    }
}