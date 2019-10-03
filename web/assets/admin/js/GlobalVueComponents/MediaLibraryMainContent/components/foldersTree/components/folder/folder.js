Vue.component('section-folder', {
    template:   "<draggable @end='$emit(\"end\", true); disabled=false' @start='$emit(\"start\", true); disabled=true' @remove='removeFromFolder()' v-model='folderModel' :options='options'>"+
                    "<div v-for='n in folderModel' :draggable='!addFolderInputShow && !disabled' class='folder' :class='{\"disabled\": disabled, \"disabledPatent\": disabledPatent, \"active\": value.id==selected}'>"+
                        "<i @click='showChildren=!showChildren' v-if='value.children.length>0' class='chevron down icon' :class='{\"closed\": !showChildren}'></i>"+
                        "<i class='folder icon'></i>"+
                        "<div v-show='!openRenameInputShow' class='name'>[[value.name]]</div>"+
                        "<div v-show='openRenameInputShow' class='renameInput'>"+
                            "<input @keyup.enter='renameFolder(value.id, newFolderName)' :id='\"input_rename_\"+value.id' @keyup.esc='hideInput()' @blur='hideInput()' v-model='newFolderName' type='text' >"+
                        "</div>"+
                        "<div class='background' @contextmenu.prevent='openMenu' @dblclick='chooseFolder()' >"+
                            "<draggable class='draggable' @add='addToFolder()' v-model='folderDroppedItems' :options='{group: \"items\"}'>"+
                                "<div v-if='false' class='items' v-for='(item, i) in folderDroppedItems'></div>"+
                            "</draggable>"+
                        "</div>"+
                        "<div class='children' :class='{\"closed\": !showChildren}' v-if='value.children && showChildren'>"+
                            "<section-folder :parent-id='value.id' @contextmenu='contextMenuHandle' :count='count+1' @delete='deleteHandle' @choose='chooseHandle' :selected='selected' @renamefolder='renameFolder' @newfolder='addFolder' @end='endDrag' @start='startDrag' @update='updateHandle' @move='moveHandle' :value='folder' :index='f'  v-for='(folder, f) in value.children'></section-folder>"+
                        "</div>"+
                        "<div v-show='addFolderInputShow' class='addFolder'>"+
                            "<input @keyup.enter='addFolder(value.id, newFolderName)' :id='\"input_\"+value.id' @keyup.esc='hideInput()' @blur='hideInput()' v-model='newFolderName' type='text' >"+
                        "</div>"+
                    "</div>"+
                "</draggable>",
    props: {
        value: {
            type: Object,
            default: function () { return {} }
        },
        index: {
            type: Number,
            default: 0
        },
        selected: {
            type: [Boolean, Number],
            default: false
        },
        count: {
            type: Number,
            default:0,
        },
        parentId: {
            type: [Boolean, Number],
            default: false
        }
    },
    data: function () {
        return {
            folderModel: [
                {
                    children: this.value.children,
                    createdAt: this.value.createdAt,
                    folderCount: this.value.folderCount,
                    id: this.value.id,
                    imagesCount: this.value.imagesCount,
                    name: this.value.name,
                    parentId: this.parentId,
                }
            ],
            folderDroppedItems: [],
            disabled: false,
            disabledPatent: false,

            addFolderInputShow: false,
            openRenameInputShow: false,
            newFolderName: '',
            showChildren: true
        }
    },
    created: function () {
        this.hideChildren();
        var that = this;
        EventBus.$on('mediaLibraryFoldersTreeContextMenu', data => {
            if (data.event=='Add folder' && data.item.id==that.value.id) {
                that.openInput();
            }
            if (data.event=='Rename' && data.item.id==that.value.id) {
                that.openRenameInput();
            }
        });
    },
    watch: {
        value: {
            handler(newVal){
                this.folderModel = [
                    {
                        children: this.value.children,
                        createdAt: this.value.createdAt,
                        folderCount: this.value.folderCount,
                        id: this.value.id,
                        imagesCount: this.value.imagesCount,
                        name: this.value.name,
                        parentId: this.parentId,
                    }
                ]
            },
            deep: true
        },
        newFolderName: function () {
            if (this.newFolderName==" ") {
                this.newFolderName=""
            }
            this.newFolderName = this.newFolderName.replace('  ', ' ');
        }
    },
    methods: {
        hideChildren: function () {
            if (this.count>4) {
                this.showChildren = false
            }
            if (this.value.children.length>8) {
                this.showChildren = false
            }
        },
        openInput: function () {
            this.addFolderInputShow=true
            setTimeout(() => {
                $('#input_'+this.value.id).focus();
            }, 100);
        },
        openRenameInput: function () {
            this.openRenameInputShow = true;
            this.newFolderName = this.value.name
            setTimeout(() => {
                $('#input_rename_'+this.value.id).focus();
            }, 100);
        },
        hideInput: function () {
            this.addFolderInputShow = false;
            this.openRenameInputShow = false;
            this.newFolderName = '';
        },
        addFolder: function (id, name) {
            name = $.trim(name)
            if (name.length>0) {
                this.$emit('newfolder', id, name)
                this.newFolderName = '';
                this.addFolderInputShow = false;
            } else {
                alert('Input folder name')
                setTimeout(() => {
                    this.openInput();
                }, 100);
                
            }
        },
        renameFolder: function (id, name) {
            name = $.trim(name)
            if (name.length>0) {
                this.$emit('renamefolder', id, name)
                this.newFolderName = '';
                this.openRenameInputShow = false;
            } else {
                alert('Input folder name')
                setTimeout(() => {
                    this.openRenameInput();
                }, 100);
                
            }
        },
        addToFolder: function () {
            var folders = [];
            var images = [];
            var parents = [];
            var parentId = null;
            for (let i = 0; i < this.folderDroppedItems.length; i++) {
                if (this.folderDroppedItems[i].type=='folder' || !this.folderDroppedItems[i].type) {
                    folders.push(this.folderDroppedItems[i].id)
                    if (this.folderDroppedItems[i].parentId) {
                        parentId = this.folderDroppedItems[i].parentId
                    }
                }
                if (this.folderDroppedItems[i].type=='image') {
                    images.push(this.folderDroppedItems[i].id)
                }
            }
            parents.push(this.value.id)
            
            
            this.$emit('move', {
                toFolder: this.value.id,
                images: images,
                folders: folders,
                parents: parents,
                parentId: parentId
            })
            this.folderDroppedItems = [];
        },
        removeFromFolder: function () {
            this.$emit('update', this.index)
        },
        startDrag: function (parent) {
            if (parent) {
                this.disabledPatent = true;
            }
            this.$emit('start', false)
        },
        endDrag: function (parent) {
            if (parent) {
                this.disabledPatent = false;
            }
            this.$emit('end', false)
        },
        chooseFolder: function () {
            this.$emit('choose', this.value.id)
        },
        chooseHandle: function (id) {
            this.$emit('choose', id)
        },
        moveHandle: function (data) {
            var data = data;
            data.parents.push(this.value.id);
            this.$emit('move', data)
        },
        updateHandle: function (index) {
            if (index!==false) {
                this.folderModel[0].children.splice(index,1);
            }
        },
        deleteHandle: function (id) {
            this.$emit('delete', id)
        },
        deleteFolder: function () {
            this.$emit('delete', this.value.id)
        },
        openMenu: function(e) {
            var that = this;
            Vue.nextTick(function() {
                that.$emit('contextmenu', {
                    x: e.x,
                    y: e.y,
                    item: that.value,
                    parents: [that.value.id],
                    eventName: 'mediaLibraryFoldersTreeContextMenu',
                    parentId: that.parentId
                });
            }.bind(this));
        },
        contextMenuHandle: function (data) {
            var data = data;
            data.parents.push(this.value.id);
            this.$emit('contextmenu', data)
        }
    },
    computed: {
        options: function () {
            if (this.addFolderInputShow) {
                return {group:{ name:"items",  pull:"clone", put:false }, disabled: true}
            } else {
                return {group:{ name:"items",  pull:"clone", put:false }}
            }
        }
    }
})