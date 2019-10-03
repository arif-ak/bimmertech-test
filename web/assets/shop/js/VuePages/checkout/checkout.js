var appCheckout = new Vue({
    el: '#appCheckout',
    components: {
        'section-emails': CheckoutEmails,
        'section-form': CheckoutForm
    },
    data: function () {
        return {
            checkoutEmails: {
                firstEmail: '',
                confirmEmail: '',
                validation: false,
                isAuth: false
            },
            form: {
                name: '',
                surname: '',
                country: '',
                phone: '',
                salesRep: '',
                vin: '',
                company: '',
                vat: '',
                comments: '',

                errors: {
                    name: false,
                    surname: false,
                    country: false,
                    phone: false,
                    vin: false
                }
            },
            token: '',
            actionUrl: '#',
            isActive: true,
            accept: false,
            errorAccept: false,
            back_to_store: '',
            payUrl: false,
            orderId: '',
            audited: false,
            validdddddd: this.$validator
        };
    },
    watch: {
        form: {
            handler(newVal, oldVal) {
                if (
                    (newVal.name != oldVal.name && newVal.errors.name) ||
                    (newVal.surname != oldVal.surname && newVal.errors.surname) ||
                    (newVal.country != oldVal.country && newVal.errors.country) ||
                    (newVal.phone != oldVal.phone && newVal.errors.phone) ||
                    (newVal.vin != oldVal.vin && newVal.errors.vin)
                ) {
                    this.auditFields();
                }
            },
            deep: true
        },
        accept: function () {

            if (!!this.accept) {
                this.errorAccept = false;
            }

        }
    },
    created: function () {
        var that = this;


        EventBus.$emit('checkLogin');
        EventBus.$on('isAuth', function (isAuth) {
            //console.log(isAuth);
            if (!isAuth) {
                that.setFieldsFromLocalStorage();
            }
        });
        EventBus.$on('user', function (user) {
            if (user) {
                that.checkoutEmails.isAuth = true;
                that.checkoutEmails.firstEmail = user.email;
                that.checkoutEmails.confirmEmail = user.email;

                var checkoutFieldsEmail = {
                    email: user.email
                }
                if (that.getDataFromLocalStorage().checkoutFieldsEmail) {
                    that.setFieldsFromLocalStorage();
                } else {
                    if (user.firstName) {
                        that.form.name = user.firstName;
                    }
                    if (user.lastName) {
                        that.form.surname = user.lastName;
                    }
                    if (user.phone) {
                        that.form.phone = user.phone;
                    }
                    if (user.currentVinNumber) {
                        that.form.vin = user.currentVinNumber;
                    } else if (user.vinNumber) {
                        that.form.vin = user.vinNumber;
                    } else {
                        EventBus.$emit('checkVinNumber');
                    }
                }
                this.audited = true;
                window.localStorage.setItem('checkoutFieldsEmail', JSON.stringify(checkoutFieldsEmail));
            } else {
                window.location.href = '/';
            }
        });
        EventBus.$on('vinNumber', response => {
            if (response) {
                this.form.vin = response;
            } else {
                that.vin = '';
            }
        });
        EventBus.$on('setNewCartData', response => {
            that.back_to_store = response.back_to_store;
        });
    },
    computed: {
        validation: function () {
            return {
                isMatch: this.checkoutEmails.validation
            };
        }
    },
    methods: {
        getDataFromLocalStorage: function () {
            var checkoutFieldsEmail = JSON.parse(window.localStorage.getItem('checkoutFieldsEmail'));
            var checkoutFields = JSON.parse(window.localStorage.getItem('checkoutFields'));
            return {
                checkoutFieldsEmail: checkoutFieldsEmail,
                checkoutFields: checkoutFields
            }
        },
        setFieldsFromLocalStorage: function () {
            var checkoutFieldsEmail = this.getDataFromLocalStorage().checkoutFieldsEmail;
            var checkoutFields = this.getDataFromLocalStorage().checkoutFields;
            if (checkoutFieldsEmail) {
                if (checkoutFieldsEmail.email) {
                    this.checkoutEmails.firstEmail = checkoutFieldsEmail.email;
                    this.checkoutEmails.confirmEmail = checkoutFieldsEmail.email;
                    if (checkoutFields) {
                        if (checkoutFields.comments) {
                            this.form.comments = checkoutFields.comments;
                        }
                        if (checkoutFields.vin) {
                            this.form.vin = checkoutFields.vin;
                        }
                        if (checkoutFields.country) {
                            this.form.country = checkoutFields.country;
                        }
                        if (checkoutFields.name) {
                            this.form.name = checkoutFields.name;
                        }
                        if (checkoutFields.phone) {
                            this.form.phone = checkoutFields.phone;
                        }
                        if (checkoutFields.salesRep) {
                            this.form.salesRep = checkoutFields.salesRep;
                        }
                        if (checkoutFields.surname) {
                            this.form.surname = checkoutFields.surname;
                        }
                    }
                }
            }
        },
        changeAudited: function (audited) {
            this.audited = audited;
        },
        sendForm: function () {
            var that = this;
            this.auditFields();
            if (!this.payUrl) {
                if (that.accept && this.auditFields()) {
                    that.openPayWindow();
                    postRequest('order-detail', {
                        email: that.checkoutEmails.firstEmail,
                        firstName: that.form.name,
                        lastName: that.form.surname,
                        country: that.form.country,
                        phone: that.form.phone,
                        salesRep: that.form.salesRep,
                        vin: that.form.vin,
                        comments: that.form.comments
                    }).then(response => {
                        that.getUrl();
                        window.localStorage.setItem('checkoutFields', false);
                        window.localStorage.setItem('checkoutFieldsEmail', false);
                    }).catch(error => {
                        console.log(error);
                        // this.reviewsLoader = false;
                    });
                }
            } else {
                this.openPayWindow();
                PayPalWindow.location = that.payUrl;
            }
        },
        getUrl: function () {
            postRequest('order-pay', {
                    signal: false
                })
                .then(response => {
                    this.payUrl = response.url;
                    this.orderId = response.orderId;
                    if (PayPalWindow !== null) {
                        PayPalWindow.location = this.payUrl;
                    } else {
                        window.location.href = this.payUrl;
                    }

                }).catch(error => {
                    // this.reviewsLoader = false;
                });
        },
        openPayWindow: function () {
            var userClosed = true;
            var that = this;
            $('#payment_step').removeClass('disabled');
            $('#details_step').addClass('disabled');
            $('#details_step').removeClass('active');
            $('.bt.modal.pay').modal('show');
            PayPalWindow = window.open("", "_blank ", "width=500,height=600");
            PayPalWindow.moveBy((window.innerWidth - 500) / 2, (window.innerHeight - 600) / 2);
            if (PayPalWindow) {
                PayPalWindow.focus();
                var timer = setInterval(function () {
                    if (PayPalWindow && PayPalWindow.location.pathname == "/order/thank-you") {
                        PayPalWindow.close();
                        userClosed = false;
                        window.location.href = '/thank-you';
                    }
                    if (PayPalWindow && PayPalWindow.closed) {
                        clearInterval(timer);
                        $('#details_step').removeClass('disabled');
                        $('#payment_step').addClass('disabled');
                        $('.bt.modal.pay').modal('hide');
                        if (userClosed) {
                            that.sendCloseEvent();
                        }
                    }
                }, 100);
            }
        },
        sendCloseEvent: function () {
            postRequest(`order-pay/close?email=${this.checkoutEmails.firstEmail} &orderId=${this.orderId}`, {})
                .then(response => {

                });
        },
        auditFields: function () {
            this.form.name = this.form.name.replace(/\s/g, '');
            this.form.surname = this.form.surname.replace(/\s/g, '');
            this.form.phone = this.form.phone.replace(/\s/g, '');
            this.form.vin = this.form.vin.replace(/\s/g, '');
            this.form.errors.name = false;
            this.form.errors.surname = false;
            this.form.errors.country = false;
            this.form.errors.phone = false;
            this.form.errors.vin = false;
            vValidator.verify(this.form.country, 'required', {
                name: 'Country'
            }).then(res => {
                if (!res.valid) {
                    this.form.errors.country = (Array.isArray(res.errors)) ? res.errors[0] : '';
                }
            });
            this.$validator.validate('name');
            this.$validator.validate('surname');
            this.$validator.validate('phone');
            this.$validator.validate('vin');
            if (this.form.name.length == 0) {
                this.form.errors.name = true;
            }
            if (this.form.surname.length == 0) {
                this.form.errors.surname = true;
            }
            if (this.form.phone.length == 0) {
                this.form.errors.phone = true;
            }
            if (this.form.vin.length == 0) {
                this.form.errors.vin = true;
            }
            this.errorAccept = !this.accept;
            var audit = true;
            for (const key in this.form.errors) {
                if (this.form.errors.hasOwnProperty(key)) {
                    if (this.form.errors[key]) {
                        audit = false;
                        break;
                    }
                }
            }
            if (this.errorAccept) {
                audit = false;
            }
            return audit;
        }
    }
});