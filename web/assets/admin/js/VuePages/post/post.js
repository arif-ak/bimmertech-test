var PostPage = new Vue({
    el: '#Post',
    data: function () {
        return {
            initialSlug: '',
            category: '',
            enabled: false,
            id: false,
            slug: '',
            metaKeywords: '',
            metaTags: '',
            metaDescription: '',
            seoText: '',
            thumbnail: {
                oneToOne: {
                    id: false,
                    name: false,
                    imageBlob: false
                },
                twoToOne: {
                    id: false,
                    name: false,
                    imageBlob: false
                }
            },
            title: '',
            content: '',
            categories: [],

            errors: {
                slug: false,
                title: false,
                category: false,
                oneToOne: false,
                twoToOne: false
            },

            loading: true,
            successMessage: false,

            previewWindow: null,
            randomNumber: Math.floor(Math.random() * (100 - 1)) + 1
        }
    },
    mounted: function () {
        var that = this;
        if (postId) {
            that.loadPost();
        } else {
            that.loading = false;
        }
        this.loadCategories();

        var created = window.localStorage.getItem('created');
        if (created == window.location.href) {
            this.successMessage = 'Post has been successfully created.'
            window.localStorage.setItem('created', false);
        }
    },
    watch: {
        slug: function () {
            this.checkSlug();
        },
        title: function (newVal) {
            this.title = $.trim(this.title)
            if (newVal.length > 01) {
                this.title = this.title.replace('  ', ' ');
            }
            if (this.title.length == 0) {
                this.errors.title = 'This field is required';
            } else {
                this.errors.title = false;
            }
            if (!this.initialSlug) {
                var slug = this.title;
                slug = slug.replace(/[&\/\\#,+()$~%.'":*?<>{}\]\[;@!^=_]/g, '');
                slug = slug.replace(/\s/g, '-');
                slug = slug.toLowerCase();
                if (slug) {
                    this.slug = this.randomNumber+"-"+slug;
                } else {
                    this.slug = this.randomNumber
                }
                
            }
        },
        content: function () {
            if (content) {
                this.updatePreview();
            }
        }
    },
    methods: {
        loadPost: function () {
            var that = this;
            getRequest('blog/post/' + postId)
                .then(response => {
                    that.category = response.category.id;
                    that.enabled = response.enabled;
                    that.id = response.id;
                    that.slug = response.slug;
                    that.initialSlug = response.slug;
                    that.metaKeywords = response.metaKeywords;
                    that.metaTags = response.metaTags;
                    that.metaDescription = response.metaDescription;
                    that.seoText = response.seoText;
                    that.thumbnail = {
                        oneToOne: {
                            id: false,
                            name: response.thumbnail.oneToOne,
                            imageBlob: false
                        },
                        twoToOne: {
                            id: false,
                            name: response.thumbnail.twoToOne,
                            imageBlob: false
                        }
                    }
                    that.title = response.title;
                    if (response.content) {
                        that.content = response.content;
                    } else {
                        that.content = '';
                    }
                    this.loading = false;
                }).catch(error => {

                });
        },
        loadCategories: function () {
            var that = this;
            getRequest('blog/categories')
                .then(response => {
                    that.categories = response;
                }).catch(error => {

                });
        },
        checkSlug: function () {
            var that = this;
            if (this.slug.length > 0) {
                if (this.initialSlug != this.slug) {
                    postRequest('blog/post/slug/check', {
                        slug: that.slug
                    }).then(response => {
                        if (response.isExist) {
                            this.errors.slug = 'This slug already exist';
                            this.loading = false;
                            return !response.isExist;
                        } else {
                            this.errors.slug = false;
                        }
                    }).catch(error => {

                    })
                }
            } else {
                this.errors.slug = 'This field is required';
            }
        },
        checkFields: function () {
            this.checkSlug();
            if (this.slug === "") {
                this.errors.slug = 'This field is required'
            }
            if (this.title === "") {
                this.errors.title = 'This field is required'
            }
            if (this.thumbnail.oneToOne.name == false) {
                this.errors.oneToOne = 'This image is required'
            }
            if (this.thumbnail.twoToOne.name == false) {
                this.errors.twoToOne = 'This image is required'
            }
            var audit = true;
            for (const key in this.errors) {
                if (this.errors.hasOwnProperty(key)) {
                    if (this.errors[key]) {
                        audit = false;
                        break;
                    }
                }
            }
            return audit
        },
        save: function () {
            var that = this;
            if (this.checkFields()) {
                that.loading = true;
                that.content = that.content.replace(/<p><\/p>/g, '<p><br /></p>');
                var data = {
                    id: that.id,
                    category: that.category,
                    title: that.title,
                    enabled: that.enabled,
                    header: that.header,
                    sub_header: that.sub_header,
                    slug: that.slug,
                    metaKeywords: that.metaKeywords,
                    metaTags: that.metaTags,
                    metaDescription: that.metaDescription,
                    seoText: that.seoText,
                    thumbnail: {
                        oneToOne: that.thumbnail.oneToOne.name,
                        twoToOne: that.thumbnail.twoToOne.name,
                    },
                    content: that.content,
                }
                if (postId) {
                    putRequest('blog/post/' + postId, data)
                        .then(response => {
                            that.loading = false;
                            that.successMessage = 'Post has been successfully updated.';
                        }).catch(error => {

                        });
                } else {
                    postRequest('blog/post', data)
                        .then(response => {
                            window.localStorage.setItem('created', window.location.href + '/' + response.id);
                            window.location.href = window.location.href + '/' + response.id;
                        }).catch(error => {

                        });
                }
            }
        },
        openInBlog: function () {
            window.open(window.location.origin + "/blog/item/"+ this.slug, "MsgWindow", "width=1000,height=500", "text/html", "replace");
        },
        openPreview: function () {
            var that = this;
            this.updatePreview();
            var timer = setInterval(function () {
                if (that.previewWindow && that.previewWindow.closed) {
                    clearInterval(timer);
                    that.previewWindow = false
                }
            }, 1000);
            if (!this.previewWindow) {
                this.previewWindow = window.open("", "MsgWindow", "width=1000,height=500", "text/html", "replace");
                this.previewWindow.focus();
                this.previewWindow.document.head.innerHTML = '<link rel="stylesheet" href="' + window.location.origin + '/assets/shop/css/custom.css">'
                this.updatePreview();
            } else {
                this.previewWindow.focus();
            }
        },
        updatePreview: function () {
            if (this.previewWindow) {
                this.previewWindow.document.clear();
                this.previewWindow.document.body.innerHTML = this.prewiew;
            }
        }
    },
    computed: {
        filteredCategoties: function () {
            var categories = []
            categories.push({
                val: 0,
                label: 'No category'
            })
            for (let i = 0; i < this.categories.length; i++) {
                if (this.categories[i].enable) {
                    categories.push({
                        val: this.categories[i].id,
                        label: this.categories[i].name
                    })
                }
            }
            return categories;
        },
        prewiew: function () {
            var content = this.content
            var replacedContent = this.content.replace(/custom-/g, '');
            replacedContent = replacedContent.replace(/<p><\/p>/g, '<p><br /></p>');
            var contentDiv = '<div class="block grid-container"><div id="blog" class="section-custom">' + replacedContent + '</div></div>';
            return contentDiv;
        }
    }
});