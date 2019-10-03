var AccountClose = {
    data: function () {
        return {
            password: '',
            error: false,
            closeStatus: false,
            list: [{
                    name: "John"
                },
                {
                    name: "Joao"
                },
                {
                    name: "Jean"
                }
            ]
        }
    },
    mounted: function () {

        $('.bt.modal.closeAccount')
            .modal({
                selector: {
                    close: '.bt-close, .actions .button'
                },
                onHidden() {
                    window.location.href = "/";
                }
            });

        EventBus.$on('isAuth', function (isAuth) {
            if (!isAuth && !that.closeStatus) {
                window.location.href = '/'
            }
        });
    },
    methods: {
        checkFields: function () {
            var that = this;

            this.password = this.password.replace(/\s/g, '');

            this.error = false;

            if (!this.password) {
                this.error = 'Please provide your password.'
            }
            if (!this.error) {
                $('.bt.modal.closeConfirm')
                    .modal({
                        selector: {
                            close: '.bt-close, .actions .button',
                            approve: '.actions .approve, .actions .ok',
                            deny: '.actions .deny, .actions .cancel'
                        },
                        centered: false,
                        useFlex: true,
                        onApprove: function () {
                            console.log('Approve');
                            that.closeAccount();
                            //EventBus.$emit('closeAccount');
                            return true;
                        },
                        onDeny: function () {
                            console.log('Deny');
                            return true;
                        },
                    }).modal('show');
            }
        },
        closeAccount: function () {
            var that = this;
            this.$http.post('/api2/user/account/close', {
                    password: that.password
                })
                .then(response => {
                    if (response.data == 'Account closed') {
                        EventBus.$emit('logOut');
                        that.closeStatus = true;
                        $('.bt.modal.closeAccount').modal('show');
                    }
                }).catch(error => {
                    if (error.data == 'Bad password') {
                        that.error = 'Wrong password';
                    }
                });
        }
    }
}