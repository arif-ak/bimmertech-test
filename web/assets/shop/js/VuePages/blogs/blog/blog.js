var Blog = new Vue({
    el: '#blog',
    data: function () {
        return {
            reviewsApi: {
                loadComments: 'blog-review',
                loadLikes: 'blog-review-like/token-filter',
                like: 'blog-review-like',
                sendComment: 'blog-review/'
            },
            recomended: [],
            post: '',
            url: window.location.href,
        }
    },
    created: function () {
        var that = this;
        EventBus.$on('documentReady', function (data) {
            if (data.loadRecomendedOnBlogPage) {
                EventBus.$on('updateCompatibility', function (vincheck) {
                    that.loadRecomended();
                });
            }
        })
    },
    mounted: function () {
        var that = this;
        // *******************************************************************************************************************
        // start fb share button
        // *******************************************************************************************************************
        window.fbAsyncInit = function () {
            FB.init({
                appId: '896721440520234',
                autoLogAppEvents: true,
                xfbml: true,
                version: 'v3.1'
            });
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    },
    methods: {
        loadRecomended: function () {
            this.$http.get('/api2/blog/post/'+slug+'/related')
            .then(response => {
                this.recomended = response.data;
            }).catch(error => {
              // this.reviewsLoader = false;
            }).finally(() => {
              // this.loadMoreBtn = this.checkLoadMoreBtn(this.productList.length);
            });
        },
        shareFB: function () {
            // FB.ui({
            //     method: 'share',
            //     display: 'popup',
            //     href: 'https://developers.facebook.com/docs/',
            // }, function (response) { });
            var win = window.open("https://www.facebook.com/sharer/sharer.php?u=" + this.url, "MsgWindow", "width=1000,height=500");
            win.focus();
        },
        shareTvitter: function () {
            var body = 'Hi! I found this on Bimmer Tech and thought you might like it! Check it out now!  ' + this.url;
            body = body.replace(/\s/g, '+');
            var win = window.open("https://twitter.com/intent/tweet?text=" + body, "MsgWindow", "width=1000,height=500");
            win.focus();
        },
        sendMail: function () {
            sendMailG('product', {
                body: 'Hi! I found this on Bimmer Tech and thought you might like it! Check it out now!  ' + this.url,
                title: 'Chek out what I found on Bimmer Tech!',
            });
        },
        shareLinkedIn: function () {
            var body = 'Hi! I found this on Bimmer Tech and thought you might like it! Check it out now!';
            body = body.replace(/\s/g, '+');
            // body = encodeURIComponent(body);
            // var currentUrl = 'http://devtestshop.bimmer-tech.net/blog/item/117-ice-drifting-is-brilliant-but-also-insane?id=117'
            // currentUrl = encodeURIComponent(currentUrl);
            var url = 'https://www.linkedin.com/shareArticle?mini=true&url='+encodeURIComponent(this.url)+'&text=This+is+google+a+search+engine';
            
            var win = window.open(url, "MsgWindow", "width=1000,height=500");
            win.focus();
        },
    }
})