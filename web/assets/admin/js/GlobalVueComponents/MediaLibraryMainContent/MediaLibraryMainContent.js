Vue.component('section-media-library', {
    props: {
        chooseMode: {
            type: Boolean,
            default: false
        }
    },
    components: {
        'section-image-show': MediaLibraryImageView,
        'section-folders-tree': foldersTree,
    },
    data: function () {
        return {
            media: [],
            sort: "type",
            order: "ASC",
            limit: 10,
            page: 1,
            pages: 1,
            loading: false,


            selectMode: false,

            selectedImages: [],
            selectedFolders: [],

            sortOptions: [
                {
                    val: 'type',
                    label: "Type"
                },
                {
                    val: 'name',
                    label: "Name"
                },
                {
                    val: 'createdAt',
                    label: "Date"
                }
            ],
            orderOptions: [
                {
                    val: 'ASC',
                    label: "Folder"
                },
                {
                    val: 'DESC',
                    label: "Image"
                }
            ],
            limitOptions: [
                {
                    val: '10',
                    label: "10"
                },
                {
                    val: '20',
                    label: "20"
                },
                {
                    val: '50',
                    label: "50"
                },
                {
                    val: '100',
                    label: "100"
                }
            ],

            selectedImage: false,
            successMessage: false,
            folders: [],

            currentFolderId: false,

            copyItems: {
                folders: [],
                images: [],
                parentId: false
            },
            emptyFolder: false,

            choosedImage: false
        }
    },
    watch: {
        currentFolderId: function () {
            this.loadMedia()
        },
        selectMode: function () {
            if (this.selectMode==false) {
                this.selectedFolders = [];
                this.selectedImages = [];
            }
        },
        sort: function () {
            if (this.sort=='type') {
                this.orderOptions = [
                    {
                        val: 'ASC',
                        label: "Folder"
                    },
                    {
                        val: 'DESC',
                        label: "Image"
                    }
                ]
            } else {
                this.orderOptions = [
                    {
                        val: 'ASC',
                        label: "A-Z"
                    },
                    {
                        val: 'DESC',
                        label: "Z-A"
                    }
                ] 
            }
        }
    },
    mounted: function () {
        this.loadFoldersTree();
        this.loadMedia();
        $('.ui.checkbox').checkbox();
        var that = this;
        EventBus.$on('uploadImageInputMediaLibrary', data => {
            data.newImage.toBlob(function (blob) {
                var formData = new FormData();
                formData.append('image', blob);
                formData.append('id', false);
                that.loading = true;
                that.$http.post('/admin/api2/media/photo/upload', formData)
                .then(response => {
                    that.successMessage = 'Image has been successfully uploaded.'
                    that.loadMedia();
                })
            });
        });
        EventBus.$on('mediaLibraryFoldersTreeContextMenu', data => {
            if (data.event=='Cut') {
                that.clearBuffer();
                that.copyItems.folders = [
                    data.item.id
                ];
                that.copyItems.images = [];
                that.copyItems.parentId = data.parentId;
                that.selectMode = false
            }
            if (data.event=='Paste') {
                var data = {
                    folders: that.copyItems.folders,
                    images: that.copyItems.images,
                    toFolder: data.item.id,
                    parents: data.parents,
                    parentId: that.copyItems.parentId
                }
                that.checkParents(data)
            }
            if (data.event=='Delete') {
                that.deleteFolder(data.item.id)
            }
        });
        EventBus.$on('mediaLibraryContextMenu', data => {
            if (data.event=='Cut') {
                that.clearBuffer();
                if (that.selectMode) {
                    that.copyItems.folders = that.selectedFolders;
                    that.copyItems.images = that.selectedImages;
                } else {
                    if (data.item.type=='folder') {
                        that.copyItems.folders = [
                            data.item.id
                        ];
                    }
                    if (data.item.type=='image') {
                        console.log(data.item);
                        that.copyItems.images = [
                            data.item.id
                        ];
                    }
                }
                that.copyItems.parentId = data.parentId;
            }
            if (data.event=='Paste') {
                var data = {
                    folders: that.copyItems.folders,
                    images: that.copyItems.images,
                    toFolder: data.item.id,
                    parents: data.parents,
                    parentId: that.copyItems.parentId
                }
                that.checkParents(data)
            }
            if (data.event=='Delete') {
                if (data.item.type=='folder') {
                    that.deleteFolder(data.item.id)
                }
                if (data.item.type=='image') {
                    
                }
                
            }
            that.selectMode = false;
        });
    },
    methods: {
        loadFoldersTree: function (loading) {
            if (loading) {
                this.loading=true;
            }
            var that = this;
            this.$http.get('/admin/api2/media/library/folders/tree')
            .then(response => {
                that.folders = [
                    {
                        id: false,
                        name: 'Root',
                        children: response.data
                    }
                ];
                if (loading) {
                    this.loading=false;
                }
            }).catch(error => {
                if (error.data=='Folders not found') {
                    that.folders = [
                        {
                            id: false,
                            name: 'Root',
                            children: []
                        }
                    ];
                }
            });
        },
        loadMedia: function (toFirstPage) {
            this.clearBuffer();
            this.selectMode = false;
            this.$emit('choose', false)
            this.choosedImage = false;
            if (!toFirstPage) {
                if (this.currentFolderId) {
                    var api = '/admin/api2/media/library/folder/'+this.currentFolderId
                } else {
                    var api = '/admin/api2/media/library/folder'
                }
                var that = this;
                that.media = [];
                this.loading = true;
                this.emptyFolder = false;
                this.$http.get(api+'?limit='+this.limit+'&page='+this.page+'&sort='+this.sort+'&order='+this.order)
                .then(response => {
                    that.media = response.data.data;
                    if (response.data.data.length==0) {
                        that.emptyFolder = true;
                    }
                    // that.limit = response.data.limit;
                    that.page = response.data.page;
                    that.pages = response.data.pages;
                }).finally(function () {
                    that.loading = false;
                    this.selectedFolders = [];
                    this.selectedImages = [];
                    this.selectMode = false;
                });
            } else {
                if (this.page==1) {
                    this.loadMedia();
                } else {
                    this.page = 1;
                }
            }
        },
        addFolder: function (id, name) {
            this.loading = true;
            this.$http.post('/admin/api2/media/library/folder', {
                name: name,
                parent: id
            })
            .then(response => {
                this.loadFoldersTree();
                this.loadMedia();
            })
        },
        renameFolder: function (id, name) {
            this.loading = true;
            this.$http.put('/admin/api2/media/library/folder/'+id, {
                name: name
            })
            .then(response => {
                this.loadFoldersTree();
                this.loadMedia();
            })
        },
        chooseFolder: function (id) {
            this.currentFolderId = id;
        },
        deleteFolder: function (id) {
            if (confirm('Are you sure delete folder?')) {
                if (this.currentFolderId==id) {
                    this.currentFolderId=false;
                }
                this.loading = true;
                this.$http.delete('/admin/api2/media/library/folder/'+id)
                .then(response => {
                    this.loadFoldersTree();
                    this.loadMedia();
                })
            }
        },
        openItem: function (i, type) {
            if (!this.selectMode) {
                if (type=='image') {
                    if (!this.chooseMode) {
                        this.selectedImage = this.media[i].path;
                        $('.ui.modal.mediaLibraryImageView').modal('show');
                    }
                } else if (type=='folder') {
                    this.currentFolderId = this.media[i].id;
                }
            }
            
        },
        changeImage: function(event) {
            var that = this;
            let file = event.target.files[0]

            EventBus.$emit("cropperFile", {
                aspectRatio: 'custom',
                file: file,          
                EventName: 'uploadImageInputMediaLibrary',
                inpId: 'uploadImageInputMediaLibrary'
            });
        },
        moveHandle: function (data) {
            if (data.folders.length>0 || data.images.length>0) {
                var data = data;
                if (this.selectMode) {
                    data.folders = this.selectedFolders;
                    data.images = this.selectedImages;
                }
                this.checkParents(data);
            }
        },
        moveSend: function (data) {
            var api
            if (data.toFolder===false) {
                api = '/admin/api2/media/library/folder/move-to-root';
            } else {
                api = '/admin/api2/media/library/folder/move-to/' + data.toFolder;
            }
            this.loading = true;
            this.$http.put(api, {
                folders: data.folders,
                images: data.images
            })
            .then(response => {
                this.loadFoldersTree();
                this.loadMedia();
            })
        },
        checkChecked: function (id, type) {
            if (type=='folder') {
                for (let i = 0; i < this.selectedFolders.length; i++) {
                    if (this.selectedFolders[i]==id) {
                        return false;
                    }
                }
            }
            if (type=='image') {
                for (let i = 0; i < this.selectedImages.length; i++) {
                    if (this.selectedImages[i]==id) {
                        return false;
                    }
                }
            }
            return true;
            
        },
        contextMenuHandle: function (data, addFolder=true) {
            var that = this;
            var buttons = [];
            // buttons.push({
            //     name: 'Cut',
            //     disabled: false
            // });
            // if (data.item.type=='image') {
            //     buttons.push({
            //         name: 'Paste',
            //         disabled: true
            //     });
            // } else {
            //     buttons.push({
            //         name: 'Paste',
            //         disabled: that.checkBuffer(data.item.id)
            //     });
            // }
            buttons.push({
                name: 'Rename',
                disabled: !addFolder
            });
            buttons.push({
                name: 'Delete',
                disabled: data.item.type=='image'
            });
            buttons.push({
                name: 'Add folder',
                disabled: !addFolder
            });
            EventBus.$emit('sectionContextMenu', {
                x: data.x,
                y: data.y,
                item: data.item,
                eventName: data.eventName,
                buttons: buttons,
                parents: data.parents,
                parentId: data.parentId
            });
        },
        checkParents: function (data) {
            var check = true;
            console.log(data);
            for (let i = 0; i < data.folders.length; i++) {
                for (let j = 0; j < data.parents.length; j++) {
                    if (data.folders[i]===data.parents[j]) {
                        check = false;
                        break;
                    }
                }
                if (data.folders[i]==data.toFolder) {
                    check = false;
                    break;
                }
            }
            if (data.parentId==null) {
                if (data.toFolder===this.currentFolderId) {
                    check = false;
                }
            } else {
                if (data.toFolder===data.parentId) {
                    check = false;
                }
            }
            
            if (check) {
                if (confirm('Are you shure?')) {
                    this.moveSend(data);
                }
            } else {
                alert('You can not move a folder to the same folder or its folders.')
            }
            this.loadFoldersTree(true);
        },
        checkBuffer: function (id) {
            var check = true;
            if (this.copyItems.folders.length>0 || this.copyItems.images.length>0) {
                check =  false;
            }
            for (let i = 0; i < this.copyItems.folders.length; i++) {
                if (this.copyItems.folders[i]==id) {
                    console.log(this.copyItems.folders[i]);
                    
                    check = false;
                }
            }
            return check;
        },
        openMenu: function (e, i) {
            var that = this;
            var check = false;
            if (that.selectMode) {
                if (that.media[i].type=='image') {
                    for (let j = 0; j < that.selectedImages.length; j++) {
                        if (that.selectedImages[j]==that.media[i].id) {
                            check = true;
                        }
                    }
                }
                if (that.media[i].type=='folder') {
                    for (let j = 0; j < that.selectedFolders.length; j++) {
                        if (that.selectedFolders[j]==that.media[i].id) {
                            check = true;
                        }
                    }
                }
            }
            if (check || !that.selectMode) {
                Vue.nextTick(function() {
                    that.contextMenuHandle({
                        x: e.x,
                        y: e.y,
                        item: that.media[i],
                        parents: [that.currentFolderId],
                        eventName: 'mediaLibraryContextMenu',
                        parentId: that.currentFolderId
                    }, false)
                }.bind(this));
            }
        },
        clearBuffer: function () {
            this.copyItems.folders = [];
            this.copyItems.images = [];
            this.copyItems.parentId = false;
        },
        chooseImage: function (i) {
            if (this.media[i].type=="image" && !this.selectMode) {
                this.choosedImage = i;
                this.$emit('choose', this.media[i].url)
            } else {
                this.choosedImage = false;
                this.$emit('choose', false)
            }
        }
    },
    computed: {
        pagesOptions: function () {
            var options = [];
            for (let i = 0; i < this.pages; i++) {
                options.push({
                    val: i+1,
                    label: i+1
                })
            }
            return options;
        }
    }
});