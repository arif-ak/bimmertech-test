var Collapsible = {
  data: function () {
    return {
        active: false
    }
  },
  methods: {
    openTab: function () {
      this.active = !this.active;
    }
  }
}

var appFooter = new Vue({
  el: '#appFooter',
  components: {
    'section-collapsible': Collapsible
  },
  data: function () {
    return {
      show: true,
      email: '',
      button: 'Subscribe',
      respMc: null,
      userLogged: false,
      loginRedirect: false,
      active: false
    }
  },
  mounted: function () {
    let _this = this;
    EventBus.$on('isAuth', function (isAuth) {
      _this.userLogged = isAuth;
    });
  },
  methods: {
    cookieAccepted: function () {
      fetch('/cookie-user-seting', {
          credentials: 'same-origin', // 'include', default: 'omit'
          method: 'GET', // 'GET', 'PUT', 'DELETE', etc.
          redirect: 'follow',
          headers: new Headers({
            'Content-Type': 'application/json'
          })
        })
        .then(data => this.show = !this.show);
    },
    checkUserAuth: function (event, option) {
      let _this = this;
      if (!_this.userLogged) {
        event.preventDefault();
        if (option) {
          _this.loginRedirect = true;
        }
        $('.user-login').popup('show');
      }
    },
    subscribe: function () {

    },
    sendMail: function () {
      sendMailG('main', {
        mailto: 'info@bimmer-tech.net',
        title: 'Bimmer Tech!'
      });
    },
    goTo: function (name, uri) {
      window.location.href = '/page-help';
      MainData.setScrollEvent(name, uri);
      MainData.setScrollEventState(true);
    }
  }
});