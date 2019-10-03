var productPage = new Vue({
    el: '#product-page-container',
    data: function () {
        return {
            product: false,
            addons: [],
            recomended: [],
            price: null,
            savePrice: false,
            selectedAddons: [],
            selectedWarranty: false,
            selectedOptions: [],
            productImgPath: null,
            updateComp: false,
            updateBuyersGuide: true,
            vincheck: 'test',
            url: window.location.href,
            product_id: null,
            cSlug: null,
            customersReviews: 0,
            averageRating: 0,
            reviewsApi: {
                loadComments: 'product-review',
                loadLikes: 'product-review-like/token-filter',
                like: 'product-review-like',
                sendComment: 'product-review/'
            }
        }
    },
    watch: {
        product: function () {
            this.updateBuyersGuide = false;
        }
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
        // *******************************************************************************************************************
        // end fb share button
        // *******************************************************************************************************************
        if (typeof (productImgPath) != "undefined" && productImgPath !== null) {
            this.productImgPath = productImgPath;
        }
        if (typeof (cSlug) != "undefined" && cSlug !== null) {
            this.cSlug = cSlug;
            this.product = {
                name: product_container_name,
                compatibility: null,
                popup_option: null
            }
            EventBus.$on('documentReady', function (data) {
                if (data.loadRecomendedOnContainerPage) {
                    EventBus.$on('updateCompatibility', function (vincheck) {
                        that.loadRecomended();
                    });
                }
            });
        }
        EventBus.$on('updateCompatibility', function (vincheck) {
            if (that.cSlug) {
                that.updateCompatibilityContainer(vincheck);
            }
        });
        EventBus.$on('vincheck', function (vincheck) {
            that.vincheck = vincheck;
        });
        EventBus.$on('customersReviews', function (customers) {
            that.customersReviews = customers;
        });
        EventBus.$on('averageRating', function (averageRating) {
            that.averageRating = averageRating;
        });
    },
    methods: {
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
            var body = 'Hi! I found this on Bimmer Tech and thought you might like it! Check it out now! ' + this.product.name + ' ' + this.url;
            body = body.replace(/\s/g, '+');
            var win = window.open("https://twitter.com/intent/tweet?text=" + body, "MsgWindow", "width=1000,height=500");
            win.focus();
        },
        sendMail: function () {
            sendMailG('product', {
                body: 'Hi! I found this on Bimmer Tech and thought you might like it! Check it out now! ' + this.product.name + ' ' + this.url,
                title: 'Chek out what I found on Bimmer Tech!',
            });
        },
        openVinchek: function () {
            $('#section-vincheck').popup('show');
        },
        updateCompatibilityContainer: function (vincheck) {
            var that = this;
            this.updateComp = true;
            postRequest('product-container/compatibility', {
                    slug: that.cSlug
                })
                .then(response => {
                    that.vincheck = vincheck;
                    that.product.compatibility = response.compatibility;
                    that.product.popup_option = response.popup_option;

                    if (response.compatibility == "Yes") {
                        window.location.reload();
                    } else {
                        that.updateComp = false;
                    }
                }).catch(error => {
                    console.log(error);
                    that.updateComp = false;
                })
        },
        loadRecomended: function () {
            if (!this.cSlug) {
              return;
            }
            this.$http.get('/api2/category-interesting-in/' + cSlug)
              .then(response => {
                this.recomended = response.data;
              }).catch(error => {}).finally(() => {});
          },

        priceMacro: (data) => {
            return (data / 100).format(2, 3);
        },
        clacPrice: function () {
            if (this.product) {
                var price = this.product.price;
                for (let i = 0; i < this.product.addons.length; i++) {
                    for (let j = 0; j < this.selectedAddons.length; j++) {
                        if (this.product.addons[i].variant_id == this.selectedAddons[j]) {
                            price += this.product.addons[i].price;
                        }
                    }
                }
                if (this.selectedWarranty) {
                    for (let i = 0; i < this.product.warranty.length; i++) {
                        if (this.product.warranty[i].variant_id == this.selectedWarranty) {
                            price += this.product.warranty[i].price;
                        }
                    }
                }
                for (let i = 0; i < this.product.dropDown.length; i++) {
                    if (this.product.dropDown[i].selectedOption != null) {
                        for (let j = 0; j < this.product.dropDown[i].options.length; j++) {
                            if (this.product.dropDown[i].options[j].id == this.product.dropDown[i].selectedOption) {
                                price += this.product.dropDown[i].options[j].price;
                            }
                        }
                    }
                }
                if (this.savePrice) {
                    price -= this.product.savePrice.price;
                }
                this.price = price;
            }
        },
        addToCart: function () {
            EventBus.$emit('addProductForCartWithAddons', {
                includedAddons: this.filteredIncludedAddons,
                productId: this.product.id,
                variantId: this.product.variant_id,
                warranty: this.selectedWarranty,
                addons: this.selectedAddons,
                savePrice: this.savePrice,
                dropDown: this.selectedOptions
            });
        },
        addSelectedOptions: function () {
            this.selectedOptions = [];
            for (let i = 0; i < this.product.dropDown.length; i++) {
                if (this.product.dropDown[i].selectedOption != null) {
                    this.selectedOptions.push(this.product.dropDown[i].selectedOption)
                }
            }
            this.clacPrice();
        }
    },
    computed: {
        IconColorClassName: function () {
            if (this.product.compatibility === null) {
                return {
                    'yellow': true
                }
            } else if (this.product.compatibility === 'Yes') {
                return {
                    'green': true
                }
            } else if (this.product.compatibility === 'No') {
                return {
                    'red': true
                }
            } else if (this.product.compatibility === 'Not sure') {
                return {
                    'blue': true
                }
            }
        },
        filteredAddons: function () {
            filteredAddons = [];
            if (this.product) {
                if (this.product.addons) {
                    for (let a = 0; a < this.product.addons.length; a++) {
                        for (let i = 0; i < this.addons.length; i++) {
                            if (this.product.addons[a].id == this.addons[i].id && this.addons[i].compatibility !== "No") {
                                filteredAddons.push(this.product.addons[a])
                            }
                        }
                    }
                }
            }
            return filteredAddons;
        },
        filteredIncludedAddons: function () {
            var includedAddons = [];
            if (this.product) {
                if (this.product.includedAddons) {
                    for (let i = 0; i < this.product.includedAddons.length; i++) {
                        includedAddons.push(this.product.includedAddons[i].variant_id);
                    }
                }
            }
            return includedAddons;
        },
        showAddons: function () {
            if (this.filteredAddons.length == 0 && this.filteredIncludedAddons.length == 0) {
                return false;
            }
            return true;
        }
    }
});