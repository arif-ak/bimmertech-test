Vue.component('section-media', {
    props: {
        value: {
            type: [Array, Object],
            default: Array
        },
        once: {
            type: Boolean,
            default: false
        },
        label: {
            type: [String],
            default: 'Media'
        },
        aspectRatio: {
            type: [Number, String],
            default: 'custom'
        },
        error: {
            type: [String, Boolean]
        },
        upload: {
            type: Boolean,
            default: false
        },
        option: {
            type: [String, Boolean],
            default: false
        }
    },
    data: function () {
        return {
            media: [],
            inputId: 'media_input_' + (Math.random().toString(36).substr(2, 9)),
            loading: false,
            inputPrettyName: false,
            parent: false,
        }
    },
    mounted: function () {
        var that = this;
        if (this.once) {
            this.media = [];
            this.media.push(this.value);
        } else {
            this.media = this.value;
        }        
        EventBus.$on(this.inputId, data => {
            data.newImage.toBlob(function (blob) {
                that.media[data.m].imageBlob = blob;
                var formData = new FormData();
                formData.append('image', blob);
                formData.append('id', that.media[data.m].id);
                if (that.upload) {
                    that.$http.post('/admin/api2/media/photo/upload', formData)
                        .then(response => {
                            that.media[data.m].name = response.data.path;
                            that.media[data.m].id = response.data.id;
                            if (that.once) {
                                that.$emit('input', that.media[0]);
                                that.$emit('change');
                            } else {
                                that.$emit('input', that.media);
                            }
                        });
                } else {
                    that.media[data.m].name = data.newImageSrc;
                    that.$emit('change', that.media[data.m].id)
                }
            });
        });
        if (this.name !== undefined) {
            const field = this.$validator.fields.find({
                id: this.id
            });
            field.update({
                name: this.name
            });
            
        }
        if (this.label !== undefined) {
            this.inputPrettyName = this.label;
        }
        if (this.name !== undefined) {
            this.inputName = this.name;
        }
    },
    watch: {
        value: {
            handler(newVal) {
                if (this.once) {
                    this.media = [];
                    this.media.push(newVal);
                } else {
                    this.media = newVal;
                }
            },
            deep: true
        },
        value: function () {
            if (this.once) {
                this.media = [];
                this.media.push(this.value);
            } else {
                this.media = this.value;
            }
        }
    },
    updated: function () {
        if (this.once) {
            this.$emit('input', this.media[0])
        } else {
            this.$emit('input', this.media)
        }
    },
    methods: {
        addMedia: function () {
            this.media.push({
                id: false,
                name: false,
                imageBlob: false
            })
        },
        delMdia: function (m) {
            var that = this;
            // if (this.media[m].id!=false) {

            // } else {
            //     this.media.splice(m,1);
            // }
            this.$emit('delete', this.media[m].id)
            this.media.splice(m, 1);

        },
        clearMedia: function () {
            this.$emit('input', {
                id: false,
                name: false,
                imageBlob: false
            })
        },
        changeImage: function (event, m) {
            var that = this;
            let file = event.target.files[0]

            EventBus.$emit("cropperFile", {
                aspectRatio: that.aspectRatio,
                file: file,
                m: m,
                inpId: that.inputId + m,
                EventName: that.inputId
            });
        },
    }
})