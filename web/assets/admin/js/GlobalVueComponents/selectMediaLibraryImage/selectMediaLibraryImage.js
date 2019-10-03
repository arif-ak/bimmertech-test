var selectMediaLibraryImage = new Vue ({
    el: '#selectMediaLibraryImage',
    data: function () {
        return {
            show: false,
            selectedImagePath: false,
            eventId: false
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$on('selectMediaLibraryImage', data => {
            that.eventId = data.id
            this.show = true;
        });
    },
    methods: {
        chooseHandle: function (url) {
            this.selectedImagePath = url;
        },
        select: function () {
            var that = this;
            EventBus.$emit("selectImageMediaLibrary", {
                id: that.eventId,
                imageUrl: that.selectedImagePath
            });
            this.selectedImage = false;
            this.show = false;
        }
    }
})