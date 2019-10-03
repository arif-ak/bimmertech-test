var appHeader = new Vue({
    el: '#appHeader',
    data: function () {
      return {
        vincheck: 'Identify your BMW/MINI',
        menuToggle: false,
        whiteHeader: false,
        // showCartPrice: true
      }
    },
    mounted: function () {
      // this.showCartPrice = false;
  
      var that = this;
      that.headerBgColor();
      $(document).scroll(function () {
        that.headerBgColor();
      });
      $(window).resize(function () {
        that.headerBgColor();
      });
  
    },
    components: {
      'section-cart': Cart,
      'section-login': Login,
      'section-vincheck': Vincheck,
      'section-search': Search
    },
    created: function () {
      var that = this;
      $('.bt.dropdown.item').dropdown({
        onHide() {
          that.menuToggle = false
        }
      });
      EventBus.$on('divider', function (divider) {
        that.divider(divider);
      });
    },
  
    methods: {
      humburger: function () {
        EventBus.$emit('humburger');
      },
      headerBgColor: function () {
        var width = $(window).width();
        if (width>=768) {
          if ($(document).scrollTop() > 0) {
            this.whiteHeader = true;
          } else {
            this.whiteHeader = false;
          };
        } else {
          this.whiteHeader = true;
        }
        
      },
      divider: function (data) {
        if (this.menuToggle == data) {
          this.menuToggle = false;
        } else {
          this.menuToggle = data;
        }
      }
    }
  
})