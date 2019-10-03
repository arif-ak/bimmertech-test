var changeWarehouse = {
    data: function () {
        return {
            action: '',
            items: {},
            selectedWarehouse: '',
            currentWarehouse: '',
            warehousesList: [],
            selected_items: [],
            loading: false,
            success: false
        }
    },
    watch: {
        loading: function () {
            if (this.loading) {
                $('.ui.modal.changeWarehouse').addClass('bt-loading');
            } else {
                $('.ui.modal.changeWarehouse').removeClass('bt-loading');
            }
        }
    },
    computed: {
        saveBtn: function () {
            return this.selected_items.length > 0 && this.selectedWarehouse !== '';
        },
        historyObj: function () {
            if (!$('.ui.modal.changeWarehouse').modal('is active') && !this.saveBtn) {
                return;
            }
            let names = '',
                re = / Warehouse/gi;
            this.selected_items.forEach(key => {
                names = names.concat(this.items.find(x => x.id === key).product_name, ', ');
            })
            return {
                template: 'changeWarehouse',
                selectedItems: names.trim().slice(0, -1),
                currentWarehouse: this.currentWarehouse.label.replace(re, ''),
                selectedWarehouse: this.warehousesList.find(item => item.val == this.selectedWarehouse).label.replace(re, ''),
            };
        },
    },
    mounted: function () {
        var that = this;
        EventBus.$on("changeWarehouse", data => {
            that.clearForm();
            that.currentWarehouse = data.warehousesList.find(item => item.val == data.id);
            that.warehousesList = data.warehousesList.filter(item => item.val != data.id);
            that.items = data.items;
            that.action = data.action;
            $('.ui.modal.changeWarehouse')
                .modal({
                    onHidden: function () {
                        $('.item-list .master.checkbox').checkbox('uncheck');
                    },
                    onVisible: function () {
                        modalCheckFunGlob();
                    }
                })
                .modal('show');
        });
    },
    methods: {
        changeWarehose: function () {
            var _this = this;
            let data = {
                order_id: orderId,
                warehouse_id: _this.selectedWarehouse
            };
            if (_this.action != 'order/change-warehouse') {
                data = Object.assign(data, {
                    order_items: _this.selected_items
                });
            } else {
                data = Object.assign(data, {
                    order_item_units: _this.selected_items
                });
            }
            if (_this.selected_items.length > 0) {
                this.loading = true;
                this.$http.post(`/admin/api2/${_this.action}`, data)
                    .then(response => {
                        EventBus.$emit('sendHistory', {
                            message: makeHistoryMessage(_this.historyObj)
                        });
                    }).catch(error => {
                        console.log(error.data);
                    }).finally(function () {
                        _this.loading = false;
                        EventBus.$emit("reloadData", 'loadWarehouses');
                        $('.ui.modal.changeWarehouse').modal('hide');
                    });
            }
        },
        clearForm: function () {
            this.warehouse = {};
            this.selectedWarehouse = '';
            this.warehousesList = [];
            this.selected_items = [];
            this.loading = false;
            this.success = false;
        }
    }
};