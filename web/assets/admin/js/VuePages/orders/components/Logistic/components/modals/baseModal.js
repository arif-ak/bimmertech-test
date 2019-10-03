var baseModal = {
    data: function () {
        return {
            items: {},
            header: '',
            action: '',
            historyAction: '',
            template: '',
            warehousesList: [],
            order_item_units: [],
            images: [],
            deletedImages: [],
            selected_items: [],
            selected_number: false,
            modalLoadedData: [],
            tracking_number: '',
            linkToInstruction: '',
            btnText: 'Save',
            sendViaEmailRadio: false,
            loading: false,
            success: false,
            errors: {
                media: ''
            },
        }
    },
    watch: {
        loading: function () {
            if (this.loading) {
                $('.ui.modal.baseModal').addClass('bt-loading');
            } else {
                $('.ui.modal.baseModal').removeClass('bt-loading');
            }
        },

        images: {
            handler(newVal) {
                if (newVal) {
                    this.checkImg(newVal);
                }
            },
            deep: true
        },
    },
    mounted: function () {
        var that = this;

        EventBus.$on("baseModal", data => {
            that.clearForm();
            that.header = data.header;
            that.items = data.items;
            that.action = data.action;
            that.images = data.images;
            that.template = data.template;
            that.btnText = data.btnText ? data.btnText : 'Save';
            $('.ui.modal.baseModal').modal({
                closable: false,
                centered: true,
                useFlex: true,
                refresh: true,
                observeChanges: true,
                onHidden: function () {
                    that.selected_number = false;
                    that.linkToInstruction = '';
                    $('.item-list .master.checkbox').checkbox('uncheck');
                },
                onVisible: function () {
                    modalCheckFunGlob();
                    $('.ui.radio.checkbox').checkbox({
                        onChecked: function () {
                            if (that.template === 'EditTrackingNumber') {
                                that.loadShipment();
                                that.selected_number = true;
                            }
                        },
                    });
                }
            }).modal('show');
        });


    },
    methods: {
        loadShipment: function () {
            var that = this;
            that.loading = true;
            let element = getArrayById(that.modalLoadedData, this.selected_items);
            if (isObject(element)) {
                that.images = that.filterImages(element.images);
                that.order_item_units = element.order_item_units;
                that.tracking_number = element.tracking_number;
                that.loading = false;
            } else {
                this.$http.get('/admin/api2/order/shipment/' + this.selected_items)
                    .then(response => {
                        that.modalLoadedData.push(response.data.data);
                        that.images = that.filterImages(response.data.data.images);
                        that.order_item_units = response.data.data.order_item_units;
                        that.tracking_number = response.data.data.tracking_number;
                        that.loading = false;
                    }).catch(error => {
                        console.log(error.data);
                    });
            }
        },
        submitFunc: function (action) {
            var that = this;
            let data = [];
            makeHistoryMessage(that.historyObj);
            data = that.dataForTemplate(that.template);
            if (that.isEditTrackingNumber(that.template)) {
                that.checkImg(that.images);
                var formData = new FormData();
                // add images
                for (var i = 0; i < that.images.length; i++) {
                    if (that.images[i].id == false && that.images[i].imageBlob != false) {
                        formData.append('images[]', that.images[i].imageBlob);
                    }
                }
                Object.keys(data).forEach(function (key) {
                    if (isObject(data[key])) {
                        let k = key,
                            e = data[key];
                        Object.keys(e).forEach(function (key) {
                            if (e[key].id) {
                                formData.append(k, e[key].id);
                            } else {
                                formData.append(k, e[key]);
                            }
                        });
                    } else {
                        formData.append(key, data[key]);
                    }
                });
                if (this.errors.media == '' && this.tracking_number != '') {
                    this.loading = true;
                    this.$http.post(`/admin/api2/${action}`, formData)
                        .then(response => {

                        }).catch(error => {
                            console.log(error.data);
                        })
                        .finally(function () {
                            that.loading = false;
                            that.eventBusForTemplate(that.template);
                            that.deleteArr(that.modalLoadedData, that.selected_items);
                            $('.item-list .master.checkbox').checkbox('uncheck');
                            $('.ui.modal.baseModal').modal('hide');
                        });
                }
            } else {
                this.loading = true;
                this.$http.post(`/admin/api2/${action}`, data)
                    .then(response => {})
                    .catch(error => {
                        console.log(error.data);
                    })
                    .finally(function () {
                        that.loading = false;
                        that.eventBusForTemplate(that.template);
                        $('.item-list .master.checkbox').checkbox('uncheck');
                        $('.ui.modal.baseModal').modal('hide');
                    });
            }
        },
        deleteShipment: function () {
            var that = this;
            that.loading = true;
            this.$http.post('/admin/api2/remove-shipment/' + that.selected_items)
                .then(response => {
                    EventBus.$emit("successMessageLogisticBoard", {
                        message: 'Shipment has been successfully removed'
                    });
                    that.loading = false;
                }).catch(error => {
                    that.loading = false;
                }).finally(function () {
                    that.loading = false;
                    EventBus.$emit('sendHistory', {
                        message: makeHistoryMessage(that.historyObj, 'remove-shipment')
                    });
                    EventBus.$emit("reloadData", 'loadWarehouses');
                    $('.ui.modal.baseModal').modal('hide');
                });
        },
        filterImages: function (images) {
            var filterImages = []
            for (let i = 0; i < images.length; i++) {
                filterImages.push({
                    id: images[i].id,
                    name: window.location.origin + images[i].name,
                    imageBlob: false
                });
            }
            return filterImages;
        },
        deleteImage: function (id) {
            var check = true;
            if (id !== false) {
                for (let i = 0; i < this.deletedImages.length; i++) {
                    if (this.deletedImages[i] == id) {
                        check = false;
                    }
                }
                if (check) {
                    this.deletedImages.push(id);
                }
            }
        },
        deleteArr: function (arr, id, imgId) {
            return arr.splice(arr.findIndex(x => x.id === id), 1);
        },
        changeImage: function (id) {
            var check = true;
            if (id !== false) {
                this.deleteImage(id);
                for (let i = 0; i < this.images.length; i++) {
                    if (this.images[i].id == id) {
                        this.images[i].id = false;
                    }
                }
            }
        },
        clearForm: function () {
            this.warehouse = {};
            this.selectedWarehouse = '';
            this.warehousesList = [];
            this.selected_items = [];
            this.order_item_units = [];
            this.images = [];
            this.loading = false;
            this.success = false;
        },
        isEditTrackingNumber: function (template) {
            return template === 'EditTrackingNumber';
        },
        dataForTemplate: function (template) {
            var that = this;
            switch (template) {
                case "EditTrackingNumber":
                    return {
                        'order_id': orderId,
                        'order_item_units[]': that.order_item_units,
                            'shipment_id': that.selected_items,
                            'removed_image_ids[]': that.deletedImages,
                            'tracking_number': that.tracking_number
                    };
                case "SendViaEmail":
                    return {
                        order_id: orderId,
                            order_items: that.selected_items
                    };
                case "SendInstructions":
                    return {
                        order_id: orderId,
                            order_items: that.selected_items,
                            send_email: that.sendViaEmailRadio,
                            instruction: !that.sendViaEmailRadio ? that.linkToInstruction : 'Sent via email'
                    };
            }
        },
        checkImg: function (data, opt) {
            if (this.template == 'SendInstructions') {
                return;
            }
            if (!data.some(elem => !elem.name)) {
                data.push({
                    id: false,
                    name: false,
                    imageBlob: false
                });
            }
            if (!(data.length > 1 && data.some(elem => !elem.name))) {
                if (this.childs[0]) {
                    this.errors.media = 'Image is required';
                }
            } else {
                this.errors.media = '';
            }
        },
        eventBusForTemplate: function (template) {
            var _this = this;
            EventBus.$emit('sendHistory', {
                message: makeHistoryMessage(_this.historyObj)
            });
            switch (template) {
                case "SendViaEmail":
                    return EventBus.$emit("reloadData", 'loadWarehouses');
                case "EditTrackingNumber":
                    return EventBus.$emit("reloadData", 'loadWarehouses');
                case "SendInstructions":
                    return EventBus.$emit("reloadData", 'loadInstructions');
            }
        },
    },
    computed: {
        childs: function () {
            return this.$children;
        },
        selectedObj: function () {
            let canEditTN = false;
            if (this.template === 'EditTrackingNumber' && this.selected_items && !isEmptyArray(this.modalLoadedData)) {
                while (!this.loading) {
                    let item = this.modalLoadedData.find(x => x.id === this.selected_items);
                    return {
                        canEditTN: isDef(item.courier)
                    };
                }
            }
            return canEditTN;
        },
        historyObj: function () {
            return {
                template: this.template,
                selected: this.selected_items,
                action: this.action,
                modalLoadedData: this.modalLoadedData,
                order_item_units: this.order_item_units,
                items: this.items,
                sendViaEmailRadio: this.sendViaEmailRadio,
                linkToInstruction: this.linkToInstruction,
                tracking_number: this.tracking_number,
                header: this.header,
            };
        },
    }
};