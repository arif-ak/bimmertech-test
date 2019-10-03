const Notes = {
    props: ['notes', 'access'],
    data: function () {
        return {
            loading: true,
            note: '',
            modal: false,
        }
    },
    mounted: function () {
        let _this = this;
        this.modal = $('.bt.modal.addNote')
            .modal({
                selector: {
                    close: '.close, .actions .deny, .actions .cancel',
                    approve: '.actions .approve, .actions .ok',
                    deny: '.actions .deny, .actions .cancel'
                },
                closable: false,
                centered: true,
                useFlex: true,
                refresh: true,
                onApprove: function () {
                    if (_this.note !== '') {
                        _this.loading = true;
                        postRequest(`order/${orderId}/order-note`, {
                                message: _this.note
                            })
                            .then(response => {
                                _this.$emit("load-notes", true);
                                _this.$emit("add", {
                                    type: 'note',
                                    message: _this.note
                                });
                                _this.modal.modal('hide');
                            }).catch(error => {});
                    } else {
                        _this.modal.modal('hide');
                    }
                    return false;
                },
                onDeny: function () {
                    return true;
                },
            });
    },
    updated: function () {
        if (Object.keys(this.notes).length > 0 && !this.modal.modal('is active')) {
            this.loading = false;
        }
    },
    methods: {
        addNoteModal: function () {
            this.note = '';
            this.modal.modal('show');
            this.loading = false;
        }
    }

};