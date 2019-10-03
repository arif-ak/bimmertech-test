var media = new Vue({
    el: '#product-media',
    data: function () {
        return {
            media: [],
            loading: true,

            dataApiGet: '',
            dataApiSet: '',
            dataApiRemove: ''
        }
    },
    mounted: function () {
        this.dataApiGet = $('#product-media').data('apiget');
        this.dataApiSet = $('#product-media').data('apiset');
        this.dataApiRemove = $('#product-media').data('apiremove');
        var that = this;
        if (this.dataApiGet.indexOf("//")!==-1) {
            that.loading = false;
            EventBus.$on("newCropperSrcMedia", data => {
                this.media[data.m].name = data.newImageSrc;
                data.newImage.toBlob(function (blob) {
                    var formData = new FormData();
                    formData.append('img', blob);
                    formData.append('id', that.media[data.m].id);
                    document.getElementById(data.inpId).value = formData;
                });
            });
        } else {
            this.loadMedia();
            EventBus.$on("newCropperSrcMedia", data => {
                this.media[data.m].name = data.newImageSrc;
    
                data.newImage.toBlob(function (blob) {
                    var formData = new FormData();
                    formData.append('img', blob);
                    formData.append('id', that.media[data.m].id);
                    that.loading = true;
                    that.$http.post(that.dataApiSet, formData)
                    .then(response => {
                        //console.log(response.data);
                        that.media[data.m].id = response.data.id;
                        that.loading = false;
                    }).catch(error => {
                        that.loading = false;
                        //console.log(error);
                    })
                    that.udateHandle();
                });
            });
        }
    },
    methods: {
        loadMedia: function () {
            var that = this;
            this.$http.get(that.dataApiGet)
            .then(
                function (response) {
                    that.media = response.data.media;
                    that.loading = false;
                    that.udateHandle();
                },
                function (response) {
                    // Error.
                    that.loading = false;
                }
            );
        },
        changeImage: function(m) {
            let files = $("#sylius_product_images_"+m+"_file")[0].files;
            let file = files[0];

            EventBus.$emit("cropperFile", {
                aspectRatio: 4/3,
                file: file,
                m: m,
                inpId: "sylius_product_images_"+m+"_file",              
                EventName: 'newCropperSrcMedia'
            });
        },
        generateBase64Avatar: function() {
            this.formData.base64 = new Buffer(
                this.formData.avatar,
                "binary"
            ).toString("base64");
        },
        addMedia: function () {
            this.media.push({
                id: false,
                name: false
            })
        },
        delMdia: function (m) {
            var that = this;
            if (this.media[m].id!=false) {
                that.loading = true;
                this.$http.delete(that.dataApiRemove+that.media[m].id)
                .then(response => {
                    that.media.splice(m,1);
                    that.loading = false;
                    that.udateHandle();
                }).catch(error => {
                    //console.log(error);
                    that.loading = false;
                })
            } else {
                that.media.splice(m,1);
                that.udateHandle();
            }
        },
        udateHandle: function () {
            if (this.media.length==0 || this.media[this.media.length-1].name) {
                this.addMedia();
            }
        }
    }
});