Vue.component('section-content-editor', {
    props: {
        value: {
            type: [Array],
            default: Array
        },
    },
    data: function () {
        return {
            blocks: [
                {
                    id: false,
                    header: "",
                    subHeader: "",
                    description: "<p></p>",
                    position: "left",
                    video: null,
                    image: {
                        id: false,
                        imageBlob: false,
                        name: ""
                    }
                }
            ],
            prewiew: "",

            previewWindow: false
        }
    },
    watch: {
        value: {
            handler(newVal){
                this.blocks = newVal;
            },
            deep: true
        },
    },
    components: {
        "section-block": Block,
        "section-menu": Menu
    },
    updated: function () {
        this.$emit('input', this.blocks)
    },
    methods: {
        addBlock: function () {
            this.blocks.push({
                description: "<p></p>",
                header: "",
                subHeader: "",
                position: "left",
                video: "",
                id: false,
                image: {
                    id: false,
                    imageBlob: false,
                    name: ""
                }
            })
        },
        deleteBlock: function (i) {
            this.blocks.splice(i,1);
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
                // this.previewWindow.document.head.innerHTML = '<link rel="stylesheet" href="'+window.location.origin+'/assets/shop/css/custom.css">'
                this.updatePreview();
            } else {
                this.previewWindow.focus();
            }
        },
        updatePreview: function () {
            this.prewiew = '';
            // this.prewiew+=
            // '<div id="blog">'+
            //     '<div class="section-custom-page">';

            //     for (let i = 0; i < this.blocks.length; i++) {
            //         this.prewiew+=
            //         '<div class="'+this.blocks[i].position+'">'+
            //             '<div class="text">';
            //             if (this.blocks[i].subHeader) {
            //                 this.prewiew+=
            //                 '<h5>'+this.blocks[i].subHeader+'</h5>';
            //             }
            //             if (this.blocks[i].header) {
            //                 this.prewiew+=
            //                 '<h2>'+this.blocks[i].header+'</h2>';
            //             }
            //             if (this.blocks[i].position=='center') {
            //                 if (this.blocks[i].video) {
            //                     this.prewiew+=this.blocks[i].video;
            //                 } else if (this.blocks[i].image.name) {
            //                 this.prewiew+=
            //                 '<img src="'+window.location.origin+'/media/image/'+this.blocks[i].image.name+'" width="300px" height="300px" />';
            //                 }
            //             }
            //             this.prewiew+=
            //                 '<div class="content">'+this.blocks[i].description+'</div>'+
            //             '</div>';
            //         if (this.blocks[i].position!='center') {
            //             if (this.blocks[i].video) {
            //                 this.prewiew+=this.blocks[i].video;
            //             } else if (this.blocks[i].image.name) {
            //             this.prewiew+=
            //             '<img src="'+window.location.origin+'/media/image/'+this.blocks[i].image.name+'" width="300px" height="300px" />';
            //             }
            //         }
            //         this.prewiew+=
            //         '</div>'              
            //     }
            //     this.prewiew+=
            //     '</div>'+
            // '</div>';
            this.prewiew+= 
                '<div id="blog">'+
                    this.blocks[0].description+
                '</div>'
            
            if (this.previewWindow) {
                this.previewWindow.document.clear();
                this.previewWindow.document.body.innerHTML = this.prewiew;
            }
        }
    }
});