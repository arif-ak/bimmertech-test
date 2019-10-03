var foldersTree = {
    props: {
        value: {
            type: Array,
            default: function () { return [] }
        },
        selected: {
            type: [Boolean, Number],
            default: false
        }
    },
    data: function () {
        return {
            folderModel: this.value
        }
    },
    watch: {
        value: {
            handler(newVal){
                this.folderModel = this.value
            },
            deep: true
        },
    },
    methods: {
        moveHandle: function (data) {
            this.$emit('move', data)
        },
        chooseHandle: function (id) {
            this.$emit('choose', id)
        },
        startHandle: function () {
            this.$emit('start')
        },
        addFolder: function (id, name) {
            this.$emit('newfolder', id, name)
        },
        renameFolder: function (id, name) {
            this.$emit('renamefolder', id, name)
        },
        deleteHandle: function (id) {
            this.$emit('delete', id)
        },
        contextMenuHandle: function (data) {
            this.$emit('contextmenu', data)
        }
    }
}