var PDFWindow;
var formTimer;
var Form = {
    data: function () {
        return {
            create: true,
            header: '',
            id: false,
            shippingMethods: [],
            order_item_units: [],
            deletedImages: [],
            selected_items: [],
            isDHL: false,
            isOther: false,
            images: [],
            method: '',
            weight: 0.5,
            length: 10,
            height: 10,
            width: 10,
            number_of_pieces: 1,
            insured_amount: "0.00",
            tracking_number: '', // ES253281294KR
            shippingMethodName: '',
            test: new VeeValidate.Validator(),
            counterForm: 0,

            success: '',
            errors: {
                media: '',
                method: '',
                weight: '',
                length: '',
                height: '',
                width: '',
                shippingMethodName: '',
                number_of_pieces: '',
                insured_amount: '',
                tracking_number: '',
                server: '',
                serverTwig: '',
                selected_items: ''
            },

            documentUrl: "",
            loading: false,
            audit: true
        }
    },
    watch: {
        loading: function () {
            if (this.loading) {
                $('.ui.modal.logisticShipmentWarehouse').addClass('bt-loading');
            } else {
                $('.ui.modal.logisticShipmentWarehouse').removeClass('bt-loading');
            }
        },
        method: function () {
            var that = this;
            if (!!this.method) {
                this.errors['method'] = '';
                let arr = that.shippingMethods;
                for (let i = 0; i < arr.length; i++) {
                    if (arr[i].id === this.method) {
                        that.changeTemplate(arr[i].code);
                        this.$validator.reset();
                    }

                }

            }
        },
        selected_items: function () {
            var that = this;
            if (!!this.selected_items) {
                this.errors['selected_items'] = '';
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
        EventBus.$on("logisticShipmentWarehouse", data => {
            that.clearErrors();
            if (that.clearForm()) {
                that.create = data.create;
                that.header = data.header;
                that.id = data.id;
                that.shippingMethods = data.shippingMethods;
                that.order_item_units = [];
                for (let i = 0; i < data.order_item_units.length; i++) {
                    that.order_item_units.push(data.order_item_units[i])
                }
                if (!data.create) {
                    that.loadShipment();
                }
                $('.ui.modal.logisticShipmentWarehouse').modal({
                    selector: {
                        close: '.close',
                        approve: '.actions .approve',
                        deny: '.actions .deny, .actions .cancel'
                    },
                    closable: false,
                    centered: true,
                    useFlex: true,
                    refresh: true,
                    allowMultiple: false,
                    observeChanges: true,
                    onApprove: function () {
                        return false;
                    },
                    onHidden: function () {
                        that.clearForm();
                        that.clearErrors();
                        that.counterForm = 0;
                        that.isDHL = false;
                        that.isOther = false;
                        $('.item-list .master.checkbox').checkbox('uncheck');
                    },
                    onDeny: function () {
                        return true;
                    },
                    onVisible: function () {
                        modalCheckFunGlob();
                    }
                }).modal('show');
            }
        });

    },
    methods: {
        loadShipment: function () {
            var that = this;
            that.loading = true;
            this.$http.get('/admin/api2/order/shipment/' + this.id)
                .then(response => {
                    console.log(response.data.data);
                    that.weight = response.data.data.dhl_weight;
                    that.images = that.filterImages(response.data.data.images);
                    that.insured_amount = response.data.data.insured_amount;
                    that.number_of_pieces = response.data.data.number_of_pieces;
                    for (let i = 0; i < response.data.data.order_item_units.length; i++) {
                        that.order_item_units.push(response.data.data.order_item_units[i]);
                    }
                    that.selected_items = that.filterSelectedItems(response.data.data.order_item_units);
                    that.method = response.data.data.ship_method.code;
                    that.tracking_number = response.data.data.tracking_number;
                    that.loading = false;
                }).catch(error => {
                    console.log(error.data);
                });
        },
        getData: function (methods, params) {
            if (!methods) return;
            let reg = /(dhl)+/gi,
                result,
                methodObj = methods.filter(obj => {
                    return obj.val === params;
                });
            result = methodObj[0].code;
            if (reg.test(result)) {
                result = 'dhl';
            } else {
                result = methodObj[0].code;
            }
            return this.dataForShippingMethod(result);
        },
        dataForShippingMethod: function (template) {
            var that = this;
            switch (template) {
                case "other":
                    return {
                        'order_id': orderId,
                        'ship_method_id': that.method,
                            'tracking_number': that.tracking_number,
                            'courier': that.shippingMethodName
                    };
                case "dhl":
                    return {
                        'order_id': orderId,
                        'dhl_weight': that.weight,
                            'length': that.length,
                            'height': that.height,
                            'width': that.width,
                            'insured_amount': that.insured_amount,
                            'number_of_pieces': that.number_of_pieces,
                            'ship_method_id': that.method,
                            'tracking_number': that.tracking_number,
                    };
            }
        },
        createShipment: function () {
            this.validateForm();
        },
        callCreateShipment: function () {
            var that = this;
            var images = [];
            for (let i = 0; i < this.images.length; i++) {
                images.push(this.images[i].imageBlob);
            }
            var formData = new FormData();
            // add images
            for (var j = 0; j < that.images.length; j++) {
                formData.append('images[]', that.images[j].imageBlob);
            }
            // add order items
            for (var i = 0; i < that.selected_items.length; i++) {
                formData.append('order_item_units[]', that.selected_items[i]);
            }
            let form;
            form = this.dataForShippingMethod(this.selectedMethodObj.code);
            that.loading = true;
            Object.keys(form).forEach(function (key) {
                formData.append(key, form[key]);
            });
            this.$http.post('/admin/api2/create-order-shipment', formData)
                .then(response => {
                    if (response.data.data.label) {
                        that.documentUrl = window.location.origin + response.data.data.label;
                    } else {
                        $('.ui.modal.logisticShipmentWarehouse').modal('hide');
                    }
                    EventBus.$emit("reloadData", 'loadWarehouses');
                    this.success = 'Shipment has been successfully created';
                    this.selectedMethodObj.number = response.data.data.tracking_number;
                    EventBus.$emit('sendHistory', {
                        message: makeHistoryMessage(this.selectedMethodObj)
                    });
                    EventBus.$emit("successMessageLogisticBoard", {
                        message: 'Shipment has been successfully created'
                    });
                    that.loading = false;
                }).catch(error => {
                    console.log(error);

                    /* if (error.data.error) {
                        that.errors.server = error.data.error;
                    } else {
                        that.errors.serverTwig = error.data;
                    } */
                    that.loading = false;
                });
        },
        updateShipment: function () {

        },
        deleteShipment: function () {
            var that = this;
            that.loading = true;
            this.$http.post('/admin/api2/remove-shipment/' + that.id)
                .then(response => {
                    $('.ui.modal.logisticShipmentWarehouse').modal('hide');
                    EventBus.$emit("loadWarehouses");
                    that.loading = false;
                }).catch(error => {
                    that.loading = false;
                });
        },
        clearForm: function () {
            this.order_item_units = [];
            this.shippingMethods = [];
            this.id = false;
            this.success = '';
            this.selected_items = [];
            this.images = [];
            this.method = '';
            this.weight = 0.5;
            this.length = 10;
            this.height = 10;
            this.width = 10;
            this.number_of_pieces = 1;
            this.insured_amount = "0.00";
            this.tracking_number = '';
            this.deletedImages = [];
            this.documentUrl = '';
            return true;
        },
        clearErrors: function () {
            var that = this;
            for (const key in this.errors) {
                if (this.errors.hasOwnProperty(key)) {
                    that.errors[key] = "";
                }
            }
        },
        validateForm: function () {
            var that = this;
            that.counterForm = +1;
            this.audit = true;
            this.clearErrors();
            let errors = new Map();
            clearTimeout(formTimer);
            let vform = that.checkImg(that.images);

            function vF(name, field, params, fieldName) {
                return vValidator.verify(field, params, {
                    name: name
                }).then(res => {
                    err = (Array.isArray(res.errors)) ? res.errors[0] : '';
                    if (!res.valid) {
                        errors.set(fieldName, err);
                    }
                    if (!that.errs.items.find(x => x.field === fieldName)) {
                        that.errors[fieldName] = err;
                    }
                });
            }
            vF('Shipping items', this.selected_items, 'required', 'selected_items');
            vF('Shipping method', this.method, 'required', 'method');
            if (this.method && !this.isDHL) {
                this.$validator.validate('shippingMethodName').then(res => {
                    if (!res) {
                        errors.set('shippingMethodName', (Array.isArray(res.errors)) ? res.errors[0] : '');
                    }
                });
                this.$validator.validate('tracking_number').then(res => {
                    if (!res) {
                        errors.set('tracking_number', (Array.isArray(res.errors)) ? res.errors[0] : '');
                    }
                });
            } else if (this.method && this.isDHL) {
                vF('Weight', this.weight, 'required', 'weight');
                vF('Number of pieces', this.number_of_pieces, 'required', 'number_of_pieces');
                vF('Insured amount', this.insured_amount, 'required', 'insured_amount');
            }
            formTimer = setTimeout(function () {
                if (vform && errors.size <= 0) {
                    return that.callCreateShipment();
                }
            }, 500);
        },
        filterImages: function (images) {
            var filterImages = []
            for (let i = 0; i < images.length; i++) {
                filterImages.push({
                    id: images[i].id,
                    name: window.location.origin + images[i].name,
                    imageBlob: false,
                })
            }
            return filterImages;
        },
        filterSelectedItems: function (items) {
            var filterSelectedItems = []
            for (let i = 0; i < items.length; i++) {
                filterSelectedItems.push(items[i].id)
            }
            return filterSelectedItems;
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
        changeTemplate: function (code) {
            if (code !== 'other') {
                this.isDHL = true;
                this.tracking_number = '';
                this.isOther = false;

            } else {
                this.isDHL = false;
                this.isOther = true;
            }
        },
        checkImg: function (data, opt) {
            let vform;
            if (!data.some(elem => !elem.name)) {
                data.push({
                    id: false,
                    name: false,
                    imageBlob: false
                });
            }
            if (this.counterForm > 0) {
                if (!(data.length > 1 && data.some(elem => !elem.name))) {
                    if (this.childs[0]) {
                        this.errors.media = 'Image is required';
                        vform = false;
                    }
                } else {
                    this.errors.media = '';
                    vform = true;
                }
            }
            return vform;
        },
    },
    computed: {
        childs: function () {
            return this.$children;
        },
        selectedMethodObj: function () {
            if (!this.method) return;
            let reg = /(dhl)+/gi,
                methodObj = (!isEmptyArray(this.filteredShipMethods)) ? this.filteredShipMethods.find(x => x.val === this.method) : [],
                methodCode = (reg.test(methodObj.code)) ? 'dhl' : methodObj.code,
                selectedItems = this.selected_items.map(key => {
                    return this.order_item_units.find(x => x.id === key).product_name.trim();
                });
            return {
                code: methodCode,
                action: 'Created',
                template: 'shipmentCreate',
                name: (methodObj.label !== 'Other') ? methodObj.label : this.shippingMethodName,
                number: (methodObj.label !== 'Other') ? 'genNumber' : this.tracking_number,
                products: selectedItems.join(', ')
            };
        },
        filteredShipMethods: function () {
            var that = this;
            var data = [];
            for (let i = 0; i < this.shippingMethods.length; i++) {
                data.push({
                    val: that.shippingMethods[i].id,
                    label: that.shippingMethods[i].name,
                    code: that.shippingMethods[i].code
                });
            }
            return data;
        }
    }
};