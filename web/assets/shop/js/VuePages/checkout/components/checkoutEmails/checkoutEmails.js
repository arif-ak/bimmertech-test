var CheckoutEmails = {
    props: ['value', 'auditedProp'],
    data: function () {
        return {
            firstEmail: '',
            confirmEmail: '',
            // password: '',
            emails: {
                first: false,
                second: false
            },
            messages: {
                registered: false,
                closed_account: false,
                mutch: false,
                restored: false
            },
            audited: false,
            isAuth: false,
            checkEmailRequest: false,
            loading: false
        }
    },
    created: function () {
        this.firstEmail = this.value.firstEmail;
        this.confirmEmail = this.value.confirmEmail;
        this.isAuth = this.value.isAuth;
    },
    watch: {
        value: {
            handler(newVal) {
                this.firstEmail = newVal.firstEmail;
                this.confirmEmail = newVal.confirmEmail;
                this.isAuth = newVal.isAuth;
            },
            deep: true
        },
        auditedProp: function () {
            this.audited = this.auditedProp;
        },
        firstEmail: function () {
            if (emailRE.test(this.firstEmail) != false) {
                this.emails.first = true;
                var checkoutFieldsEmail = JSON.parse(window.localStorage.getItem('checkoutFieldsEmail'));
                var checkoutFieldsEmailEmail = ''
                if (checkoutFieldsEmail) {
                    if (checkoutFieldsEmail.email) {
                        checkoutFieldsEmailEmail = checkoutFieldsEmail.email
                    }
                }
                if (checkoutFieldsEmailEmail != this.firstEmail) {
                    this.checkEmail();
                } else {
                    this.checkEmailRequest = true;
                    this.messages.registered = false;
                    this.isMatchFunc();
                }
                // this.messages.registered = Boolean(Math.round(Math.random()));
                // this.messages.registered = false;
            } else {
                this.emails.first = false;
                this.messages.registered = false;
                this.messages.closed_account = false;
            }
            this.isMatchFunc();
        },
        confirmEmail: function () {
            if (emailRE.test(this.confirmEmail) != false) {
                this.emails.second = true;
            } else {
                this.emails.second = false;
            }
            this.isMatchFunc();
        },
        isAuth: function () {
            if (this.isAuth) {
                this.audited = true;
                this.messages.registered = false;
                this.messages.closed_account = false;
            } else {
                if (emailRE.test(this.firstEmail) != false) {
                    this.checkEmail();
                }
            }
        },
        audited: function () {
            if (this.audited && !this.isAuth) {
                this.registerFunc();
            }
            this.$emit('audited', this.audited);
        },
        checkEmailRequest: function () {
            this.isMatchFunc();
        }
    },
    computed: {
        isMatch: function () {
            return this.firstEmail == this.confirmEmail && (this.emails.first && this.emails.second)
        }
    },
    methods: {
        auditConfirmEmail: function () {
            if (this.firstEmail != this.confirmEmail) {
                this.messages.mutch = true;
            } else {
                this.messages.mutch = false;
            }
        },
        isMatchFunc: function () {
            if (this.isMatch && !this.messages.registered && this.checkEmailRequest || this.isAuth) {
                this.auditConfirmEmail();
                setTimeout(() => {
                    this.audited = true;
                    this.updateState();
                }, 500);
            } else {
                this.audited = false;
                this.updateState();
            }
        },
        updateState: function () {
            var that = this;
            this.$emit('input', {
                firstEmail: that.firstEmail,
                confirmEmail: that.confirmEmail,
                validation: that.audited,
                isAuth: that.isAuth
            });
        },
        openLogin: function (option) {
            console.log(option);
            if (option) {
                console.log(option + ' 2');
                EventBus.$emit('hasLoginData', option);
            }

            $('.user-login').popup('show');
        },
        checkEmail: function () {
            if (!this.isAuth) {
                this.checkEmailRequest = false;
                var that = this;
                this.$http.get('/api2/email/find?email=' + this.firstEmail, {
                    before(request) {
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then(response => {
                    that.checkEmailRequest = true;
                    that.messages.registered = response.data.registered;
                    that.messages.closed_account = response.data.closed_account;
                    that.isMatchFunc();
                }).catch(error => {

                }).finally(() => (that.checkEmailRequest = true));
            } else {
                this.messages.registered = false;
                this.messages.closed_account = false;
                this.isMatchFunc();
            }
        },
        restoreFunc: function () {
            if (!!this.firstEmail) {
                this.loading = true;
                this.$http.post('/api2/user/restore-account', {
                        email: this.firstEmail
                    })
                    .then(response => {
                        if (response.data.data) {
                            this.checkEmail();
                            this.messages.restored = response.data.data;
                        }
                        if (response.data.error) {
                            this.messages.restored = response.data.error;
                        }
                        //this.loginFormState = 'regSuccess';

                    }).catch(error => {

                        this.messages.restored = error.body
                    }).finally(() => (this.loading = false));
            }
        },
        registerFunc: function () {
            this.$http.post('/api2/registration-ajax', {
                    email: this.firstEmail
                })
                .then(response => {
                    this.regMessage = response.data;
                    this.loginFormState = 'regSuccess';
                    this.setStorage();
                }).catch(error => {
                    this.regMessage = error.body
                }).finally(() => (this.loading = false));
        },
        setStorage: function () {
            var checkoutFieldsEmail = {
                email: this.firstEmail
            }
            window.localStorage.setItem('checkoutFieldsEmail', JSON.stringify(checkoutFieldsEmail));
        }
    }
}