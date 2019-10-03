var contextMenu = new Vue ({
    el: '#section-context-menu',
    data: function () {
        return {
            show: false,
            top: '0px',
            left: '0px',
            item: false,
            eventName: false,
            buttons: [],
            parents: [],
            parentId: null
        }
    },
    watch: {
        show: function (current, old) {
            let that = this;

            if (old === false) {
              setTimeout(() => {
                document.addEventListener("click", that.closeMenu, false);
              }, 250);
            } else {
              document.removeEventListener("click", that.closeMenu, false);
            }
        }
    },
    mounted: function () {
        var that = this;
        EventBus.$on('sectionContextMenu', data => {
            that.show = true;
            that.setMenu(data.y, data.x)
            that.$refs.context.focus();
            that.item = data.item;
            that.eventName = data.eventName;
            that.buttons = data.buttons;
            that.parents = data.parents;
            that.parentId = data.parentId;
        });
    },
    methods: {
        setMenu: function(top, left) {
            largestHeight = window.innerHeight - this.$refs.context.offsetHeight - 25;
            largestWidth = window.innerWidth - this.$refs.context.offsetWidth - 25;
            if (top > largestHeight) top = largestHeight;
            if (left > largestWidth) left = largestWidth;
            this.top = top + 'px';
            this.left = left + 'px';
        },
        closeMenu: function () {
            this.show = false;
        },
        testClick: function () {
            console.log('testClick');
            
        },
        returnEvent: function (button) {
            if (!button.disabled) {
                EventBus.$emit(this.eventName, {
                    event: button.name,
                    item: this.item,
                    parents: this.parents,
                    parentId: this.parentId
                });
            }
        }
    }
})