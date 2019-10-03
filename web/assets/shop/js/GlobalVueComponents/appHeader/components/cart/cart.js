var Cart = {
  data: function () {
    return {
      cartList: false,
      cartTotal: 0,
      message: true,
      pricetText: '',

      loading: false
    }
  },
  created: function () {
    var that = this;
    EventBus.$on('documentReady', function (data) {
      if (data.loadCart) {
        that.loadCart();
      }
    })
    EventBus.$on('addProductForCart', productId => {
      $('html').addClass('bt-loading');
      getRequest('cart/add?productId=' + productId)
        .then(response => { // Success.          
          this.cartList = response.cart_items
          this.uItems = response.cart_items.length
          this.cartTotal = response.cart_total
        }).finally(() => {
          $('html').removeClass('bt-loading');

        });
      });
    EventBus.$on('addProductForCartWithAddons', product => {
      $('html').addClass('bt-loading');
      postRequest('cart/add', {
          includedAddons: product.includedAddons,
          productId: product.productId,
          variantId: product.variantId,
          warranty: product.warranty,
          addons: product.addons,
          savePrice: product.savePrice,
          dropDown: product.dropDown
        })
        .then(data => { // Success.
          this.cartList = data.cart_items;
          this.uItems = data.cart_items.length;
          this.cartTotal = data.cart_total;
        }) // Result from the `response.json()` call
        .catch(error => console.error(error))
        .finally(() => {
          $('html').removeClass('bt-loading');
          $('#sylius-cart-button').popup('show');
        });
    });
    EventBus.$on('updateCart', function () {
      that.loadCart();
    });
  },
  methods: {
    loadCart: function () {
      getRequest('cart')
        .then(response => { // Success.
          if (this.cartList.length > 0 && response.cart_items.length == 0) {
            $('#sylius-cart-button').popup('hide');
            setTimeout(() => {
              this.cartList = response.cart_items;
              this.cartTotal = response.cart_total;
              EventBus.$emit('setNewCartData', response);
              this.loading = false;
            }, 50);
          } else {
            this.cartList = response.cart_items;
            this.cartTotal = response.cart_total;
            EventBus.$emit('setNewCartData', response);
            this.loading = false;
          }
        }).catch((error) => {
          return;
        })

    },
    removeProduct: function (id) {
      this.loading = true;
      deleteRequest('cart/remove/item/' + id)
        .then(response =>
          this.loadCart()
        ).catch(error => {
          this.reviewsLoader = false;
        })
    },
    priceMacro: (data) => {
      return (data / 100).format(2, 3);
    }
    // itemPieces: (data) => {
    //   if (data == 1) {
    //     return 'pc'
    //   } else {
    //     return 'pcs'
    //   }
    // },

  },
  computed: {
    uItems: function () {
      // var count = 0
      // if (this.cartList) {
      //   for (let i = 0; i < this.cartList.cart_items.length; i++) {
      //     console.log(this.cartList.cart_items[i].quantity);

      //     count += this.cartList.cart_items[i].quantity
      //   }
      // }
      if (this.cartList) {
        return this.cartList.length;
      } else {
        return 0
      }
    }
  }
};