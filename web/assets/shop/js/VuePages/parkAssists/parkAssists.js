var productListComp = new Vue({
  el: '#taxons',
  data: {
    productList: [],
    recomended: [],
    vincheck: '',
    count: 0
  },
  mounted: function () {
    /*     var that = this;
        EventBus.$on('updateCompatibility', function (vincheck) {
          that.vincheck = vincheck;
          if (typeof (taxonSlug) != "undefined" && taxonSlug !== null) {
            that.loadProductList();
          }
        }); */
    var that = this;
    EventBus.$on('updateCompatibility', function (vincheck) {
      that.vincheck = vincheck;
      if (typeof (taxonSlug) != "undefined" && taxonSlug !== null) {
        that.loadProductList();
      }
    });
    EventBus.$on('documentReady', function (data) {
      if (data.loadRecomendedOnTaxonPage) {
        EventBus.$on('updateCompatibility', function (vincheck) {
          that.loadRecomended();
        });
      }
    });
  },
  created: function () {

  },
  methods: {
    loadMore: function (el) {
      this.count = 1;
      this.loadProductList();
    },

    loadProductList: function () {
      if (!this.tSlug) {
        return;
      }
      var that = this;
      //this.productList = [];
      fetch('/api2/category/' + taxonSlug, {
          headers: {
            "Content-Type": "application/json; charset=utf-8"
          },
          method: 'POST',
          body: JSON.stringify({
            page: that.count
          })
        })
        .then(response => response.json())
        .then(data => {
          var products = [];
          var productContainers = [];
          if (data.products) {
            products = data.products;
          }
          if (data.productContainers) {
            productContainers = data.productContainers;
          }
          var list = this.parseFun(products, productContainers);
          this.productList = [];
          for (let i = 0; i < list.length; i++) {
            this.productList.push(list[i])
          }
        }).catch(error => {
          // this.reviewsLoader = false;
        })

    },
    loadRecomended: function () {
      if (!this.tSlug) {
        return;
      }
      this.$http.get('/api2/category-interesting-in/' + taxonSlug)
        .then(response => {
          this.recomended = response.data;
        }).catch(error => {}).finally(() => {});
    },
    parseFun(products, productsContainer) {
      var container = []
      for (let index = 0; index < productsContainer.length; index++) {
        container.push(productsContainer[index]);
      }
      for (let index = 0; index < products.length; index++) {
        container.push(products[index]);
      }
      return container;
    }
  },
  computed: {
    loadMoreBtn: function () {
      if (this.productList.length == 8) {
        return true;
      } else {
        return false;
      }
    },
    tSlug: function () {
      return typeof (taxonSlug) != "undefined" && taxonSlug !== null;
    }
  }
});