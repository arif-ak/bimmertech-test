var AccountInstruction = {
    data: function () {
        return {
            items: []
        }
    },
    mounted: function () {
        this.loadItems();
        let _this = this;
        EventBus.$on('codingResponse', function (coding) {
            if (coding) {
                _this.items = coding;
            }
        });
    },
    methods: {
        loadItems: function () {
            let _this = this;
            getRequest('user/coding')
                .then(data => {
                    _this.items = _this.parseItems(data);
                }).catch(error => {});
        },
        filterItem: function (item) {
            if (isNA(item.coding) && isNA(item.coding_session) && isNA(item.instruction.statusMessage)) {
                return false;
            }

            function isNA(element) {
                return element === 'n/a';
            }
        },
        isNAFunc: function (data) {
            if (data !== 'n/a') {
                return data;
            }
        },
        isCompleted: function (data) {
            return data == 'completed';
        },
        parseItems: function (data) {
            let _this = this
            if (!isNullOrUndefined(data)) {
                data.forEach(element => {
                    if (!isEmptyArray(element.orderItems)) {
                        element.orderItems.forEach(item => {
                            item.coding_session = {
                                message: (item.coding_session !== 'n/a') ? 'schedule' : item.coding_session,
                                url: (item.coding_session.match(urlRE)) ? item.coding_session : null
                            };
                            item.coding_session.message = (item.coding == 'completed') ? item.coding : item.coding_session.message;
                            removeFromArray(item.addons, n => _this.filterItem(n) === false);
                            removeFromArray(item.includedAddons, n => _this.filterItem(n) === false);
                            removeFromArray(item.dropDowns, n => n === 'n/a');
                        });
                    }
                });
            }
            return data;
        }
    }
};