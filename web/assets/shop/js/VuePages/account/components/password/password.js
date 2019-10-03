var AccountPassword = {
    data: function () {
        return {
            oldPassword: '',
            newPassword: '',
            confirmPassword: '',
            validatorEB: new VeeValidate.ErrorBag(),
            errors: {
                oldPassword: false,
                newPassword: false,
                confirmPassword: false
            }
        }
    },
    mounted: function () {
        $('.bt.modal.passChanged')
            .modal({
                selector: {
                    close: '.bt-close, .actions .button'
                },
            })
    },
    methods: {
        checkFields: function () {
            this.oldPassword = this.oldPassword.replace(/\s/g, '');
            this.newPassword = this.newPassword.replace(/\s/g, '');
            this.confirmPassword = this.confirmPassword.replace(/\s/g, '');

            this.errors.oldPassword = false;
            this.errors.newPassword = false;
            this.errors.confirmPassword = false;

            if (!this.oldPassword) {
                this.errors.oldPassword = 'Please provide your old password.';
            }
            if (!this.newPassword) {
                this.errors.newPassword = 'A new password is required.';
            }
            if (!this.confirmPassword) {
                this.errors.confirmPassword = 'Please confirm your new password.';
            }
            if (this.newPassword && this.confirmPassword) {
                if (this.confirmPassword != this.newPassword) {
                    this.errors.confirmPassword = 'The passwords don’t match. Please check and try again. ';
                }
            }
            var audit = true;
            for (const key in this.errors) {
                if (this.errors.hasOwnProperty(key)) {
                    if (this.errors[key]) {
                        audit = false;
                        break;
                    }
                }
            }
            if (audit) {
                this.changePassword();
            }
        },
        changePassword: function () {
            var that = this;
            this.$http.post('/api2/user/password/change', {
                    password: that.oldPassword,
                    newPassword: that.newPassword
                })
                .then(response => {
                    if (response.data == 'Password changed') {
                        that.oldPassword = '';
                        that.newPassword = '';
                        that.confirmPassword = '';
                        $('.bt.modal.passChanged').modal('show');
                    }
                }).catch(error => {
                    if (error.data == 'Bad password') {
                        that.errors.oldPassword = 'Wrong password';
                    }
                });
        }
    }
}