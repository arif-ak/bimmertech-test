Vue.component('section-ask', {
    data: function () {
      return {
        space: /([^\s])/,
  
        name: '',
        email: '',
        vin: '',
        message: '',
        apiUrl: '/api2/ask-bmw-expert/sales_email',
        loading: false,
        sendMessage: null,
  
        emailError: '',
        nameError: '',
        vinError: '',
        messageError: '',
        sendFirstClick: false
      }
    },
    mounted: function () {
      var that = this;
      //EventBus.$emit('checkLogin');
      EventBus.$on('user', function (user) {
        if (user) {
          that.email = user.email;
          if (user.firstName) {
              that.name=user.firstName;
          }
          // if (that.vin=="") {
            if (user.currentVinNumber) {
              that.vin=user.currentVinNumber;
            } else if (user.vinNumber) {
              that.vin=user.vinNumber;
            } else {
              EventBus.$emit('checkVinNumber');
            }
          // }
        } else {
          that.clearInp();
          EventBus.$emit('checkVinNumber');
        }
      });
      EventBus.$on('vinNumber', response => {
          that.vin=response;
      });
      EventBus.$on('selectModalAskExpertTab', function (tab) {
        console.log(tab);
        
        if (tab=='SALES') {
          that.apiUrl = '/api2/ask-bmw-expert/sales_email';
        } else if (tab=='SUPPORT') {
          that.apiUrl = '/api2/ask-bmw-expert/support_email';          
        }
      })
    },
    watch: {
      name: function () {
        if (this.sendFirstClick) {
          this.auditName();
        }
      },
      email: function () {
        if (this.sendFirstClick) {
          this.auditEmail();
        }
      },
      vin: function () {
        this.vin = this.vin.replace(/\s/g, '');
        this.vin = this.vin.replace(/[&\/\\#,+()$~%.'":*?<>{}\]\[;@!^=_-]/g, '');
        if (this.sendFirstClick) {
          this.auditVin();
        }
      },
      message: function () {
        if (this.sendFirstClick) {
          this.auditMessage();
        }
      }
    },
    methods: {
      clearInp: function () {
        this.sendFirstClick = false;
        this.name = '';
        this.email = '';
        this.vin = '';
        this.message = '';
        this.emailError = '';
        this.nameError = '';
        this.vinError = '';
        this.messageError = '';
        this.sendMessage = '';
      },
      auditName: function () {
        if (this.space.test(this.name) == false) {
          this.nameError = '';
        } else if (this.name.length < 50) {
          this.nameError = '';
        } else {
          this.nameError = 'The name is too long';
        }
      },
      auditEmail: function () {
        var regular_email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (regular_email.test(this.email) == false) {
          this.emailError = 'The e-mail seems incorrect. Please try again.';
        } else {
          this.emailError = '';
        }
      },
      auditVin: function () {
        if (this.space.test(this.vin) != false && this.vin.length >= 7 && this.vin.length <= 17) {
          this.vinError = '';
        } else {
          this.vinError = 'The VIN seems incorrect. Give a full VIN number or the last 7 digits'
        }
      },
      auditMessage: function () {
        // if (this.space.test(this.message) != false && this.message.length > 0 && this.message.length <= 10000) {
        //   this.messageError = '';
        // } else {
        //   this.messageError = true;
        // }
        if (this.space.test(this.message) == false || this.message.length == 0) {
          this.messageError = 'Enter your message.';
        } else if (this.message.length > 10000) {
          this.messageError = 'The message is too long';
        } else {
          this.messageError = '';
        }
      },
      send: function () {
        var that = this;
        this.sendFirstClick = true;
        this.auditName();
        this.auditEmail();
        this.auditVin();
        this.auditMessage();
        if (!this.nameError && !this.emailError && !this.vinError && !this.messageError) {
          this.loading = true;
          this.$http.post(this.apiUrl, {
              name: this.name,
              email: this.email,
              vin: this.vin,
              message: this.message
            })
            .then(response => {
              this.loading = false;
              that.clearInp();
              EventBus.$emit('checkLogin');
              that.sendMessage = true;
            }).catch(error => {
              that.sendMessage = false;
            }).finally(() => (this.loading = false));
        } else {
          that.sendMessage = false;
        }
      }
    }
})