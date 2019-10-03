var CheckoutForm = {
    props: ['value'],
    data: function () {
        return {
            name: '',
            surname: '',
            country: '',
            phone: '',
            salesRep: '',
            vin: '',
            company: '',
            vat: '',
            comments: '',
            
            countries: [],
            salesReps: [],

            errors: {
                name: false,
                surname: false,
                country: false,
                phone: false,
                vin: false
            }
        }
    },
    created: function () {
        this.name = this.value.name;
        this.surname = this.value.surname;
        this.country = this.value.country;
        this.phone = this.value.phone;
        this.salesRep = this.value.salesRep;
        this.vin = this.value.vin;
        this.company = this.value.company;
        this.vat = this.value.vat;
        this.comments = this.value.comments;

        this.errors = this.value.errors
        this.loadCounties();
        this.loadPersons();
    },
    updated: function () {
        this.updateState();
    },
    watch: {
        value: {
            handler(newVal){
                this.comments = newVal.comments
                this.company = newVal.company
                this.country = newVal.country
                this.name = newVal.name
                this.phone = newVal.phone
                this.salesRep = newVal.salesRep
                this.surname = newVal.surname
                this.vat = newVal.vat
                this.vin = newVal.vin
                this.errors = newVal.errors

                this.setStorage();
            },
            deep: true
        }
    },
    methods: {
        setStorage: function () {
            var checkoutFields = {
                comments: this.comments,
                company: this.company,
                country: this.country,
                name: this.name,
                phone: this.phone,
                salesRep: this.salesRep,
                surname: this.surname,
                vat: this.vat,
                vin: this.vin
            }
            window.localStorage.setItem('checkoutFields', JSON.stringify(checkoutFields));
        },
        updateState: function () {
            var that = this;
            this.$emit('input', {
                name: that.name,
                surname: that.surname,
                country: that.country,
                phone: that.phone,
                salesRep: that.salesRep,
                vin: that.vin,
                company: that.company,
                vat: that.vat,
                comments: that.comments,
                errors: that.errors
            });
        },
        loadCounties: function () {
            var that = this;
            this.$http.get('/api2/countries')
            .then(response => {
                var newVal = [];
                for (let i = 0; i < response.data.length; i++) {
                    newVal.push(
                        {
                            val: response.data[i].code,
                            label: response.data[i].name
                        }
                    )
                }
                this.countries = newVal;
            }).catch(error => {
                // this.reviewsLoader = false;
            })
        },
        loadPersons: function () {
            var that = this;
            this.$http.get('/api2/sales-person')
            .then(response => {
                var newVal = [];
                for (let i = 0; i < response.data.length; i++) {
                    newVal.push(
                        {
                            val: response.data[i].id,
                            label: response.data[i].name
                        }
                    )
                }
                this.salesReps = newVal;
            }).catch(error => {
                // this.reviewsLoader = false;
            })
        }
    }
}