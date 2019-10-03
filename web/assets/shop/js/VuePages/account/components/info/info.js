var AccountInfo = {
    data: function () {
        return {
            name: '',
            surname: '',
            email: '',
            phone: '',
            vin: '',
            birthday: '2018-12-29',
            company: '',
            vat: '',
            gender: false,
            errors: {
                name: false,
                surname: false,
                email: false,
                phone: false,
                vin: false,
                birthday: false,
            }
        }
    },
    mounted: function () {
        var that = this;
        $('.bt.modal.contactDetails')
        .modal({
            selector: {
                close: '.bt-close, .actions .button'
            }
        })
        EventBus.$emit('checkLogin');
        EventBus.$on('user', function (user) {
            if (user) {
                that.email=user.email;
                if (user.firstName) {
                    that.name=user.firstName;
                }
                if (user.lastName) {
                    that.surname=user.lastName;
                }
                if (user.phone) {
                    that.phone=user.phone;
                }
                if (user.vinNumber) {
                    that.vin=user.vinNumber;
                }
                if (user.birthday) {
                    that.birthday=user.birthday;
                }
                if (user.company) {
                    that.company=user.company;
                }
                if (user.vatNumber) {
                    that.vat=user.vatNumber;
                }
                if (user.gender) {
                    that.gender=user.gender;
                }
            } else {
                // that.checkoutEmails.isAuth = false;
                // that.checkoutEmails.firstEmail = '';
                // that.checkoutEmails.confirmEmail = '';
                // that.form.name='';
                // that.form.surname='';
                window.location.href = '/'
            }
        });
    },
    methods: {
        checkFields: function () {
            this.name = this.name.replace(/\s/g, '');
            this.surname = this.surname.replace(/\s/g, '');
            this.email = this.email.replace(/\s/g, '');
            this.phone = this.phone.replace(/\s/g, '');
            this.vin = this.vin.replace(/\s/g, '');
            this.birthday = this.birthday.replace(/\s/g, '');

            this.errors.name = false;
            this.errors.surname = false;
            this.errors.email = false;
            this.errors.phone = false;
            this.errors.vin = false;
            this.errors.birthday = false;

            if (!this.name) {
                this.errors.name = 'The name field is required'
            }
            if (!this.surname) {
                this.errors.surname = 'The surname field is required'
            }
            if (!this.email) {
                this.errors.email = 'The email field is required'
            }
            if (!this.phone) {
                this.errors.phone = 'The phone field is required'
            }
            if (!this.vin) {
                this.errors.vin = 'The vehicle identification number field is required'
            }
            if (!this.birthday) {
                this.errors.birthday = 'The birthday field is required'
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
                this.updateAccount();
            }
        },
        updateAccount: function () {
            var that = this;
            this.$http.put('/api2/user/personal/info', {
                firstName: that.name,
                lastName: that.surname,
                phoneNumber: that.phone,
                email: that.email,
                gender: that.gender,
                birthday: that.birthday,
                company: that.company,
                vinNumber: that.vin,
                vatNumber: that.vat
            })
            .then(response => {
                $('.bt.modal.contactDetails').modal('show');
            }).catch(error => {
                if (error.data=='Bad password') {
                    that.error = 'Wrong password';
                }
            });
        }
    }
}