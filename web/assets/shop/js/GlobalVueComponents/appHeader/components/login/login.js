var Login = {
  data: function () {
    return {
      loginFormState: '',
      logMessage: '',
      message: '',
      regMessage: '',
      successMessage: '',
      recoveryMessage: '',
      loading: false,
      resend: false,
      isAuth: false,

      email: '',
      emailConfirmation: '',
      password: '',

      fields: {
        emailFirst: false,
        emailSecond: false
      }
    };
  },
  watch: {
    email: function () {
      if (emailRE.test(this.email) != false) {
        this.fields.emailFirst = true;
      } else {
        this.fields.emailFirst = false;
      }
    },
    isAuth: {
      handler(newVal) {
        this.$session.set('isAuth', newVal);
        if (!newVal) {
          this.$session.set('clearByUser', newVal);
        }
      },
      deep: true
    },
    emailConfirmation: function () {
      if (emailRE.test(this.emailConfirmation) != false) {
        this.fields.emailSecond = true;
      } else {
        this.fields.emailSecond = false;
      }
    }
  },
  created: function () {
    var that = this;
    EventBus.$on('isAuth', function (isAuth) {
      this.$session.set('isAuth', isAuth);
    });
    EventBus.$on('documentReady', function (data) {
      if (data.checkLogin) {
        that.checkLogin();
      }
      if (window.location.href.endsWith('#')) {
        window.history.replaceState(null, "Title", window.location.pathname);
        setTimeout(() => {
          $('.user-login').popup('show');
        }, 2000);
      }
    })
    EventBus.$on('logOut', function () {
      that.logOut();
    });
    EventBus.$on('checkLogin', function () {
      that.checkLogin();
    });
    EventBus.$on('hasLoginData', function (data) {
      that.email = data;
    });
  },
  methods: {
    registerFunc: function () {
      if (!this.email) {
        this.regMessage = 'The email field is required.';
      }
      if (!this.emailConfirmation) {
        this.regMessage = 'The email confirmation field is required.';
      }
      if (this.fields.emailFirst && this.fields.emailSecond && this.email == this.emailConfirmation) {
        this.loading = true;
        this.$http.post('/api2/registration-ajax', {
            email: this.email
          })
          .then(response => {
            this.regMessage = response.data;
            this.loginFormState = 'regSuccess';
          }).catch(error => {
            this.regMessage = error.body;
          }).finally(() => (this.loading = false));
      }
    },
    resendFunc: function () {
      if (!!this.email) {
        this.loading = true;
        this.$http.post('/api2/resend-link', {
            email: this.email
          })
          .then(response => {
            (this.recoveryMessage = response.data);

          }).catch(error => {
            (this.recoveryMessage = error.response.data);

          }).finally(() => {
            this.message = '';
            this.resend = false;
            this.loading = false;
          });
      }
    },
    recoveryFunc: function () {
      var that = this;
      if (this.fields.emailFirst) {
        this.loading = true;
        this.$http.post('/api2/forgot-password-ajax', {
            email: this.email
          })
          .then(response => {
            this.recoveryMessage = response.data;
          }).catch(error => {
            this.recoveryMessage = error.body;
          }).finally(() => (this.loading = false));
      } else {
        this.recoveryMessage = '';
      }
    },
    loginFunc: function () {
      if (!this.email) {
        this.message = 'The email field is required.';
      }
      if (!this.password) {
        this.message = 'The password field is required.';
      }
      if (!!this.email && !!this.password) {
        this.loading = true;
        this.$http.post('/login-check', {
            _username: this.email,
            _password: this.password,
            _remember_me: 1
          })
          .then(response => {
            (this.message = response.message);
            if (response.data.success) {
              $('.login-popup').addClass('auth');
              $('.user-login').popup('toggle');
              this.loginFormState = 'logSuccess';
              this.checkLogin();
              EventBus.$emit('updateCart');
              if (appFooter.loginRedirect) {
                window.location.href = window.location.origin + "/account/dashboard";
              }
              if (window.location.pathname === '/confirmation-page') {
                window.location.href = window.location.origin + "/account/dashboard";
              }
              window.localStorage.setItem('checkoutFields', false);
              window.localStorage.setItem('checkoutFieldsEmail', false);
            }
          }).catch(error => {
            if (error.body.message == 'Invalid credentials.') {
              this.message = 'Wrong password or email address. Please try again';
              this.resend = false;
            } else if (error.body.message == 'Account is disabled.') {
              this.message = '* You didn’t confirm your email. Please, check your inbox and click the confirmation link or ';
              this.resend = true;
            } else {
              this.resend = false;
              this.message = error.body.message;
            }
          }).finally(() => {
            this.loading = false;
          });
      } else {
        return this.logMessage = 'Please provide your email and password';
      }
    },
    checkLogin: function () {
      this.$http.get('/api2/check-login')
        .then(response => {
          // Success.
          if (response.data) {
            this.loginFormState = 'logSuccess';
            this.isAuth = true;
            //eventBusLoop(['isAuth:true']);
            EventBus.$emit('isAuth', true);
            EventBus.$emit('user', response.data);
            if (!response.data.currentVinNumber && !this.$session.get('clearByUser')) {
              EventBus.$emit('checkVin', response.data.vinNumber);
            }
            $('.login-popup').addClass('auth');
          } else {
            this.loginFormState = 'login';
          }
        }).catch(error => {
          this.isAuth = false;
          EventBus.$emit('isAuth', false);
          $('.login-popup').removeClass('auth');
          this.loginFormState = 'login';
        }).finally(() => (this.loading = false));
    },
    logOut: function () {
      this.loading = true;
      this.$http.get('/logout')
        .then(response => {
          // Success.
          if (response.data) {
            this.loginFormState = 'login';
            $('.user-login').popup('hide');
            $('.login-popup').removeClass('auth');
            this.isAuth = false;
            EventBus.$emit('isAuth', false);
            EventBus.$emit('user', false);
            EventBus.$emit('clearVin');
            EventBus.$emit('updateCart');
            window.localStorage.setItem('checkoutFields', false);
            window.localStorage.setItem('checkoutFieldsEmail', false);
          }
        }).catch(error => {}).finally(() => (this.loading = false));
    }
  },

};