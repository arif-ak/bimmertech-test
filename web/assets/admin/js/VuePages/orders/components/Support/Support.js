Support = {
    props: ['access'],
    data: function () {
        return {
            loading: false,
            allInstructions: [],
            instructions: [],
            sendedInstructions: [],
            successMessage: false
        }
    },
    components: {
        'section-instruction': Instruction,
    },
    mounted: function () {
        EventBus.$on("loadInstructions", data => {
            this.loadInstructions();
        });
        EventBus.$on("reloadData", data => {
            if (data == "loadInstructions" || data == 'all') {
                this.loadInstructions();
            }
        });
        this.loadInstructions();
    },
    methods: {
        loadInstructions: function () {
            var that = this;
            this.loading = true
            this.$http.get('/admin/api2/order/' + orderId + '/support-board')
                .then(response => {
                    that.allInstructions = response.data.data;
                    that.instructions = that.filterIstructions(response.data.data);
                    that.sendedInstructions = that.filterIstructions(response.data.data, true);
                }).catch(error => {

                }).finally(function () {
                    that.loading = false;
                });
        },
        updateInstruction: function (items, option) {
            EventBus.$emit("baseModal", {
                header: option ? 'Send instructions' : 'Edit instructions',
                action: option ? 'order/add-instruction' : 'order/add-instruction',
                items: items,
                images: this.getImages(items),
                template: 'SendInstructions',
                btnText: option ? 'Send' : 'Resend',
            });
        },
        getImages: function (items) {
            var that = this;
            let result = [];
            for (var i = 0; i < items.length; i++) {
                if (items[i].shipping_images.length > 0) {
                    for (var j = 0; j < items[i].shipping_images.length; j++) {
                        result.push(items[i].shipping_images[j]);
                    }
                }
            }
            return result;
        },
        filterIstructions: function (items, option) {
            var that = this;
            let result = [];
            for (var i = 0; i < items.length; i++) {
                if (option && !!items[i].date) {
                    result.push(items[i]);
                } else if (!option && !items[i].date) {
                    result.push(items[i]);
                }
            }
            return result;
        },
    }
};
