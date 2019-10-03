var inputTimer;

PaymentModal = {
    props: ['paymentItems', 'balanceMap', 'access'],
    data: function () {
        return {
            loading: false,
            isEdit: false,
            editData: {},
            selected_items_ref: [],
            selected_items_ret: [],
            perv_selected_items_ret: [],
            mirror_modal: {
                reason: false,
                items_ret: [],
                items_ref: [],
            },
            checkBoxes: false,
            reason: '',
            amount: '',
            fieldAmount: false,
            lastAmount: '',
            percent: '',
            fieldPrecent: false,
            lastPercent: '',
            active: false,
            priority: false,
            validateFields: ['amount', 'percent'],
            validateFieldsObj: false,
            lastActive: false,
            modalActive: false,
            test_total: false,
            test_reCalc: false,
            test_cents: false,
        }
    },
    watch: {
        loading: function () {
            if (this.loading) {
                //$('.bt.modal.orderPayment').addClass('bt-loading');
            } else {
                //$('.bt.modal.orderPayment').removeClass('bt-loading');
            }
        },
        editData: {
            handler(newVal) {
                if (newVal) {
                    if (this.isEdit) {
                        this.reason = this.editData.reason;
                        this.amount = this.editData.total;
                        this.percent = this.editData.percent;
                    } else {
                        this.reason = '';
                        this.amount = '';
                        this.percent = '';
                    }
                    //  console.log(newVal);
                }
            },
            deep: true
        },
        selected_items_ref: function () {
            if (this.isEdit) {
                return;
            }
            if (!this.selected) {
                this.amount = '';
                this.percent = '';
                this.changeFieldRules(this.validateFieldsObj, false);
                return;
            }
            this.changeFieldRules(this.validateFieldsObj, true);
            this.calc(null, 'fromSelect', false, true);
        },
        selected_items_ret: {
            handler(newVal) {
                if (newVal) {
                    // console.log(newVal);

                    //this.checkRetItems(newVal);
                }
            },
            deep: true
        },
    },
    mounted: function () {
        const _this = this;
        _this.initFieldsValidation();
        EventBus.$on("refundsModal", data => {
            this.refundsModal(data);
        });
        $('.bt.modal.orderPayment')
            .modal({
                selector: {
                    close: '.actions .cancel, .close',
                    deny: '.actions .deny, .actions .cancel'
                },
                closable: false,
                centered: true,
                useFlex: true,
                allowMultiple: false,
                refresh: true,
                observeChanges: true,
                onHidden: function () {
                    _this.disableReturnedMain(false);
                    _this.resetModalFields();
                    _this.modalActive = false;
                    _this.isEdit = false;
                    _this.mirror_modal.items_ret = [];
                    _this.mirror_modal.items_ref = [];
                    _this.mirror_modal.reason = '';
                    $('.item-list.disabled .master.checkbox').checkbox('set enabled');
                    let child = $('.item-list.disabled .child.checkbox');
                    child.each(function () {
                        $(this).checkbox('set enabled');
                        $(this).checkbox('uncheck');
                    });
                    $('.item-list .checkbox').each(function () {
                        if ($(this).checkbox('is checked')) {
                            if ($(this).checkbox('can change')) {
                                $(this).checkbox('uncheck');
                            }
                        }
                    })
                },
                onShow: function () {
                    _this.checkBoxes = {
                        master: [$('.refunded+.item-list .master.checkbox'), $('.returned+.item-list .master.checkbox')],
                        child_refunded: $('.refunded+.item-list .master.checkbox').closest('.checkbox').siblings('.list').find('.checkbox'),
                        child_returned: $('.returned+.item-list .master.checkbox').closest('.checkbox').siblings('.list').find('.checkbox'),
                        child: $('.item-list .child.checkbox')
                            .checkbox({
                                fireOnInit: true // Fire on load to set parent value
                            })
                    };
                    _this.checkBoxes.master.forEach(element => {
                        $(element).checkbox({
                            onChecked: function () {
                               modalCheckFun(this, 'check');
                            },
                            onUnchecked: function () {
                               modalCheckFun(this, 'uncheck');
                            }
                        });
                    });
                    _this.checkBoxes.child_returned.each(function () {
                        if (this.dataset.returned) {
                            $(this).checkbox('check');
                            if (this.dataset.edit) {
                                $(this).checkbox('set enabled');
                            } else {
                                $(this).checkbox('set disabled');
                            }
                        }
                    });
                },
                onVisible: function () {
                    _this.mirror_modal.items_ret = _this.selected_items_ret;
                    _this.mirror_modal.items_ref = _this.selected_items_ref;
                    _this.mirror_modal.reason = _this.reason;
                    _this.modalActive = true;
                    $('.item-list.disabled .master.checkbox').checkbox('set disabled');
                    let child = $('.item-list.disabled .child.checkbox');
                    child.each(function () {
                        $(this).checkbox('set disabled');
                    });
                }
            });
    },
    computed: {
        selected: function () {
            return this.selected_items_ref.length > 0;
        },
        selected2: function () {
            return ((this.amount && this.percent) <= 0); //this.$children;
        },
        checkBoxes1: function () {
            return this.checkBoxes;
        },
        changesObj: function () {
            let info = {};
            if (this.modalActive) {
                info.quantityRet = this.selected_items_ret.length !== this.mirror_modal.items_ret.length;
                info.quantityRef = this.selected_items_ref.length !== this.mirror_modal.items_ref.length;
                if (this.isEdit) {
                    info.reason = this.reason !== this.mirror_modal.reason;
                } else {
                    info.quantityRef = ((this.amount || this.percent) <= 0) ? false : true;
                    delete info.reason;
                }
                if (this.selected_items_ret.length > 0) {
                    info.sameItemsSelected = !this.selected_items_ret.every(elem => this.balanceMap.get(elem).is_returned);
                }
            }
            return info;
        },
        changesObj2: function () {
            let info = {};
            if (this.modalActive) {
                info.quantityRet = difference(this.selected_items_ret, this.mirror_modal.items_ret).length > 0;
                info.quantityRef = difference(this.selected_items_ref, this.mirror_modal.items_ref);
                if (this.isEdit) {
                    info.reason = this.reason !== this.mirror_modal.reason;
                } else {
                    info.quantityRef = ((this.amount || this.percent) <= 0) ? false : true;
                    delete info.reason;
                }
                if (this.selected_items_ret.length > 0) {
                    info.sameItemsSelected = !this.selected_items_ret.every(elem => this.balanceMap.get(elem).is_returned);
                }
            }
            return info;
        },
        historyObj: function () {
            let _this = this;
            const diff_ret = function (editState) {
                a = _this.selected_items_ret
                b = _this.mirror_modal.items_ret
                if (editState) {
                    result = difference(b, a).concat(difference(a, b));
                } else {
                    result = difference(a, b);
                }
                return getNamesFromMapById(result, _this.balanceMap);
            };
            return {
                template: 'ReturnedRefunded',
                selectedItemsRef: getNamesFromMapById(difference(this.selected_items_ref, this.mirror_modal.items_ref), this.balanceMap),
                selectedItemsRet: diff_ret(this.isEdit),
                balanceMap: this.balanceMap,
                isEdit: this.isEdit,
                amount: Number(this.amount),
                reason: this.reason
            };
        },
        saveBtn: function () {
            return Object.keys(this.changesObj).every(elem => this.changesObj[elem] != true);
        },
        maxAmountValue: function () {
            let _this = this,
                amount = 0;
            if (typeof _this.paymentItems !== 'boolean') {
                this.selected_items_ref.forEach(function (id) {
                    return amount += _this.balanceMap.get(id).rest_of_balance;
                });
            }
            if (this.isEdit) {
                return this.editData.total;
            }
            return amount / 100;
        },
        total: function () {
            let _this = this,
                t = 0,
                totalArr = Array.from(_this.paymentItems);
            totalArr.forEach(function (i) {
                return t += i.rest_of_balance;
            });
            return t;
        },
        toPercent: function () {
            if (this.isEdit) {
                return;
            }
            if (!this.selected) {
                return;
            }
            let n = this.amount,
                max = this.maxAmountValue;
            let p = (n / max).evalN();
            return Number(p.toFixed(2));
        },
        fromPercent: function () {
            if (this.isEdit) {
                return;
            }
            if (!this.selected) {
                return;
            }
            let n = Number(this.percent),
                max = this.maxAmountValue;
            let p = (n * max);
            return Number((Number(p) / 100).toFixed(2));
        },
        priorityPercent: function () {
            if (this.priority == 'percent') {
                return this.percent;
            }
            if (this.priority == 'amount') {
                return (this.amount / this.maxAmountValue).evalN();
            }
        }
    },
    methods: {
        testFunc: function () {

        },
        parseData: function (data, select) {
            let arr = [];
            for (const i in data) {
                if (data.hasOwnProperty(i)) {
                    let inner = data[i];
                    for (const key in inner) {
                        inner[key].select = (i == 'order_item_units_free') ? true : false;
                        arr.push(inner[key]);
                    }
                }
            }
            return arr;
        },
        initFieldsValidation: function () {
            let _this = this;
            _this.validateFieldsObj = [];
            _this.validateFields.forEach(item => {
                let e = this.$validator.fields.find({
                    name: item
                });
                _this.validateFieldsObj.push(e);
            });
        },
        doReCalc: function (name, value) {
            let _this = this;
            clearTimeout(inputTimer);
            inputTimer = setTimeout(function () {
                if (name == 'amount') {
                    _this.amount = value;
                } else {
                    _this.percent = value;
                }
                _this.calc(Number(value), name, name, false);
            }, 500);
        },
        calc: function (val, fromWho, priority, reCalc) {
            let _this = this;
            if (!this.selected) {
                return;
            }
            _this.priority = !priority ? priority : priority;
            if (fromWho == 'fromSelect') {
                _this.amount = _this.maxAmountValue;
                _this.percent = 100;
                _this.priority = 'percent';
                _this.test_cents = false;
                _this.$validator.reset();
            }
            if (fromWho == 'amount') {
                if (val > _this.maxAmountValue) {
                    _this.amount = _this.maxAmountValue;
                    _this.percent = 100;
                } else {
                    _this.percent = _this.toPercent;
                }
            }
            if (fromWho == 'percent') {
                _this.amount = (val > 100) ? _this.maxAmountValue : _this.fromPercent;
            }
        },
        eachItemCalc: function (id, value, percent) {
            let sum = value * percent,
                rounded = Math.round(value * percent);
            if (!Number.isInteger(sum)) {
                if (rounded > sum) {
                    if (!this.test_cents) {
                        this.test_cents = true;
                        rounded = this.checkTotal(rounded, this.test_total);
                        return rounded;
                    }
                    rounded = rounded - 1;
                    this.test_cents = false;
                    rounded = this.checkTotal(rounded, this.test_total);
                    return rounded;
                }
                this.test_cents = false;
                rounded = this.checkTotal(rounded, this.test_total);
                return rounded;
            }
            sum = this.checkTotal(sum, this.test_total);
            return sum;
        },
        checkTotal: function (itemVal, total) {
            total -= itemVal;
            if (total == 0) {
                this.test_total -= itemVal;
                return itemVal;
            }
            if (total == 1) {
                this.test_total -= itemVal + 1;
                return itemVal + 1;
            }
            if (total == -1) {
                this.test_total = itemVal - 1;
                return itemVal - 1;
            }
            this.test_total -= itemVal;
            return itemVal;
        },
        createRefund: function () {
            let _this = this;
            _this.$validator.validateAll().then(valid => {
                if (valid) {
                    return _this.sendForm();
                }
            });
        },

        sendForm: function (options) {
            let _this = this,
                arr = [],
                totalToSend = Math.round(_this.maxAmountValue * _this.priorityPercent);
            _this.loading = true;

            if (typeof _this.paymentItems !== 'boolean' && !this.isEdit) {
                _this.test_total = Math.round(_this.maxAmountValue * _this.priorityPercent);
                _this.selected_items_ref.forEach(function (id) {
                    let item = _this.balanceMap.get(id).rest_of_balance / 100,
                        itemVal = _this.eachItemCalc(id, _this.priorityPercent, item);
                    return arr.push({
                        order_item_unit: id,
                        value: itemVal
                    });
                });
            };

            data = {
                order_id: orderId,
                order_item_unit_refund: arr,
                order_item_unit_return: _this.selected_items_ret,
                percent: _this.priorityPercent ? Number(_this.priorityPercent) : 0,
                total: totalToSend ? totalToSend : 0,
                comment: _this.reason
            };
            if (this.isEdit) {
                delete data.order_item_unit_refund;
                data.order_refund_id = this.editData.id;
                this.$http.post('/admin/api2/order/refund-edit', data)
                    .then(response => {
                        _this.loading = false;
                        if (response) {
                            $('.bt.modal.orderPayment').modal('hide');
                            EventBus.$emit("reloadData", 'all');
                            EventBus.$emit('sendHistory', {
                                message: makeHistoryMessage(_this.historyObj, 'refund', _this.isEdit)
                            });
                        }
                    }).catch(error => {
                        console.log(error);
                        _this.loading = false;
                    });
            } else {
                this.$http.post('/admin/api2/order/refund-create', data)
                    .then(response => {
                        _this.loading = false;
                        if (response) {
                            $('.bt.modal.orderPayment').modal('hide');
                            EventBus.$emit("reloadData", 'all');
                            EventBus.$emit('sendHistory', {
                                message: makeHistoryMessage(_this.historyObj, 'refund', _this.isEdit)
                            });
                        }
                    }).catch(error => {
                        console.log(error);
                        _this.loading = false;
                    });
            }
        },
        fromRefToRetArrow: function () {
            if (this.isEdit) {
                return;
            }
            let _this = this;
            removeFromArray(_this.selected_items_ret, n => $(`.returned+.item-list input[value="${n}"]`).parent().checkbox('can change'));
            this.selected_items_ref.forEach(function (id) {
                if (!_this.selected_items_ret.includes(id)) {
                    _this.selected_items_ret.push(id);
                }
            });
        },
        addFieldRule: function (rules, required) {
            if (required) {
                rules.required = true;
            } else if (rules.required) {
                delete rules.required;
            }
            return rules;
        },
        changeFieldRules: function (names, required) {
            let _this = this;
            let arr = Array.from(names);
            arr.forEach(element => {
                element.reset();
                element.rules = _this.addFieldRule(element.rules, required);
                element.update({
                    name: element.name,
                    classNames: {
                        invalid: 'is-danger'
                    }
                });
            });
            return;
        },
        resetModalFields: function () {
            let _this = this;
            _this.amount = '';
            _this.percent = '';
            _this.reason = '';
            _this.editData = false;
            _this.priority = false;
            _this.selected_items_ref = [];
        },
        disableReturnedMain: function (options) {
            let checkBoxList = $('.item-list .child.checkbox');
            checkBoxList.each(function () {
                if ($(this).checkbox('is checked')) {
                    if (this.dataset.returned) {
                        if (!options.edit) {
                            $(this).checkbox('set disabled');
                        } else {
                            $(this).checkbox('set enabled');
                        }
                    }
                }
            });
        },
        refundsModal: function (data) {
            let _this = this;
            this.isEdit = data.edit;
            _this.disableReturnedMain(data);
            if (data.edit) {
                getRequestWithAbort(`order-refund/${data.id}`, false)
                    .then(response => {
                        let res = response.data,
                            modified = {
                                'id': res.id,
                                'total': res.total / 100,
                                'percent': res.percent,
                                'reason': res.comment,
                                'items': this.parseData(res.order_unit_items, true),
                            };
                        this.editData = modified;
                        $('.bt.modal.orderPayment').modal('show');
                    }).catch(error => {});
            } else {
                $('.bt.modal.orderPayment').modal('show');
            }

        }
    }
};