Vue.component('section-rating', {
  props: ['reviews-props'],
  data: function () {
    return {
      reviews: {
        averageRating: 0,
        ratings: [0, 0, 0, 0, 0]
      },
      reviewsLoader: true,
      slug: null
    }
  },
  mounted: function () {
    var that = this;
    if (this.reviewsProps) {
      this.reviews = this.reviewsProps;
      this.reviewsLoader = false;
    } else {
      EventBus.$on('documentReady', function (data) {
        if (data.loadReviews) {
          if (typeof(product_id) != "undefined" && product_id !== null) {
            that.loadReviews();
          }
          if (typeof(slug) != "undefined" && slug !== null) {
            that.slug = slug;
            that.loadReviewsContainer();
          }
        }
      })
      
    }
  },
  computed: {
    customers: function () {
      var customers = 0;
      for (let i = 0; i < this.reviews.ratings.length; i++) {
        customers += parseInt(this.reviews.ratings[i]);
      }
      EventBus.$emit('customersReviews', customers);
      return customers;
    },
    averageRating: function () {
      if (this.reviews.averageRating) {
        var averageRating = Math.round(parseFloat(this.reviews.averageRating));
        EventBus.$emit('averageRating', averageRating);
        return averageRating;
      } else {
        EventBus.$emit('averageRating', 0);
        return 0;
      }
    }
  },
  methods: {
    loadReviews: function () {
      this.reviewsLoader = true;
      getRequest('product-rating/' + product_id)
      .then(response => {
        this.reviews = response.reviews;
        this.reviewsLoader = false;

      }).catch(error => {
        this.reviewsLoader = false;
      })
      // EventBus.$emit('addProductForCartWithAddons', {
      //   productId: this.product.id,
      //   variantId: this.product.variant_id,
      //   warranty: this.selectedWarranty,
      //   addons: this.selectedAddons,
      //   savePrice: this.savePrice
      //   dropDown: this.selectedOptions
      // });
    },
    loadReviewsContainer: function () {
      var that = this;
      this.reviewsLoader = true;
      postRequest('product-container/rating', {
        slug: that.slug,
        signal: true
      })
      .then(response => {        
        this.reviews = response.reviews;
        this.reviewsLoader = false;

      }).catch(error => {
        this.reviewsLoader = false;
      })
    }
  }
})