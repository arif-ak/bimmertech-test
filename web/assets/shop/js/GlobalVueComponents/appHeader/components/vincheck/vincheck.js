var Vincheck = {
  props: {
    menuToggle: {
      type: [Boolean, String],
      default: false
    }
  },
  data: function () {
    return {
      vin: '',
      VincheckModels: store.get('CarModels'),
      ChangedModel: 'Model',
      //route: 'sylius_shop_homepage',
      inpRegEmailError: false,
      inpRegReEmailError: false,
      inpLogEmailError: false,
      inpLogPassError: false,
      loading: false,
      up: true,
      selectedModel: '',
      vincheck: 'Identify your BMW/MINI'
    }
  },
  watch: {
    vin: function () {
      if (isDef(this.vin)) {
        this.vin = this.vin.replace(/\s/g, '').replace(/[&\/\\#,+()$~%.'":*?<>{}\]\[;@!^=_-]/g, '');
      }
    }
  },
  created: function () {
    var that = this;
    EventBus.$on('documentReady', function (data) {
      if (data.getCarModel) {
        that.getCarModel();
      }
    })
    EventBus.$on('checkVinNumber', function () {
      that.getCarModel();
    });
    EventBus.$on('checkVin', vinNumber => {
      that.checkVinFunc(vinNumber);
    });
    EventBus.$on('clearVin', function () {
      that.ClearModel();
    });
  },
  methods: {
    ChangeModel: function () {
      if (this.up) {
        var that = this;
        that.vin = '';
        this.loading = true;
        if (this.selectedModel != 'Identify your BMW/MINI' && this.selectedModel != '') {
          this.$http.get('/api2/check-car-model-ajax?model=' + this.selectedModel)
            .then(
              function (response) {
                // Success.
                // console.log(response.data)
                that.vincheck = response.data;
                $('#section-vincheck').popup('hide')
                // this.$emit('menutoggle', 'vincheck')
                EventBus.$emit('updateCompatibility', response.data);
              }
            ).finally(() => (this.loading = false))
        } else {
          this.loading = false;
        }
      }
    },
    checkVinFunc: function (vinNumber) {
      var that = this;
      this.loading = true;

      getRequest('check-vin-ajax?vin=' + (vinNumber ? vinNumber : that.vin))
        .then(data => {
          that.vincheck = data.label;
          that.vin = data.vinNumber;
          store.set('vin', {
            "number": data.vinNumber,
            "label": data.label,
          })
          $('#section-vincheck').popup('hide')
          // this.$http.get('/api2/get-session')
          //     .then(response=> console.log(response));
          EventBus.$emit('vinNumber', data.vinNumber);
          EventBus.$emit('updateCompatibility', data.label);
          EventBus.$emit('checkLogin');
          // console.log(response);
        }).catch(error => {

        }).finally(() => (this.loading = false));
      this.up = false;
      that.selectedModel = '';
      // $('#vin-dropdown.ui.cell.dropdown').dropdown('clear');
      this.up = true;
    },
    getCarModel: function () {
      var that = this;
      getRequest('get-car-model')
        .then(data => {
          if (data instanceof Object) {
            this.vin = data.vinNumber;
            this.vincheck = data.label;
            EventBus.$emit('vincheck', data.label);
            EventBus.$emit('vinNumber', data.vinNumber);
            EventBus.$emit('updateCompatibility', data.label);
          } else {
            EventBus.$emit('updateCompatibility', '');
          }
        }).catch(error => {
          console.log(error);
        });
    },
    ClearModel: function () {
      var that = this;
      this.vin = '';
      this.vincheck = 'Identify your BMW/MINI';
      this.selectedModel = '';
      if (this.$session.get('isAuth')) {
        this.$session.set('clearByUser', true);
      }
      MainData.setVin('');
      getRequest('/vincheck/clear')
        .then(res => {
          EventBus.$emit('updateCompatibility', '');
          EventBus.$emit('checkLogin');
        });
      /* this.$http.get('/vincheck/clear')
        .then(
          function (response) {
            EventBus.$emit('updateCompatibility', '');
            EventBus.$emit('checkLogin');
          },
          function (response) {
            // Error.
            console.log(response.data);
            console.log('An error occurred.');
          }
        ) */
    }
  }
}