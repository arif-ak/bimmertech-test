var CartCompatibility = {
    props: ['compatibility']
}

var CartPage = new Vue ({
    el: '#cart-page',
    data: function () {
        return {
            carModel: '',
            cartItems: [],
            back_to_store: '',
            cartTotal: false,
            cartSubTotal: false,
            loading: true,
            newVinchek: false,
            code: ''
        }
    },
    components: {
        "section-compatibility": CartCompatibility,
        "section-total": CartTotal,
        "section-code": CartCode
    },
    created: function () {
        var that = this;
        EventBus.$on('updateCompatibility', function (vincheck) {
            that.carModel = vincheck;
            if (that.newVinchek) {
                EventBus.$emit('updateCart');
            }
            that.newVinchek = true;
        });
        EventBus.$on('setNewCartData', response => {
            that.cartItems = response.cart_items;
            that.cartTotal = response.cart_total;
            that.cartSubTotal = response.cart_sub_total;
            that.back_to_store = response.back_to_store;
            that.loading = false;
            console.log(that.cartItems);
            
        });
    },
    methods: {
        sendCode: function () {
            alert('send')
        },
        priceMacro: (data) => {
            return (data / 100).format(2, 3);
        },
        removeProduct: function (id) {
            this.loading = true;
            this.$http.delete('/api2/cart/remove/item/'+id)
            .then(response => {
                EventBus.$emit('updateCart');
            }).catch(error => {
                // this.reviewsLoader = false;
            })
        },
        changeWarranty: function (id, warrantyId) {
            this.loading = true;
            this.$http.put('/api2/cart/update/warranty', {
                id: id,
                warrantyId: warrantyId
            })
            .then(response => {
                EventBus.$emit('updateCart');
            }).catch(error => {
                // this.reviewsLoader = false;
            })
        },
        clearCart: function () {
            this.loading = true;
            this.$http.put('/api2/cart/clear')
            .then(response => {
                EventBus.$emit('updateCart');
                //window.location.reload();
            }).catch(error => {
                // this.reviewsLoader = false;
            })
        },
        updateQuantity: function (id, quantity) {
            this.loading = true;
            
            this.$http.put('/api2/cart/update', {
                id: id,
                quantity: quantity
            })
            .then(response => {
                EventBus.$emit('updateCart');
            }).catch(error => {
                // this.reviewsLoader = false;
                EventBus.$emit('updateCart');
            })
        },
    },
    computed: {
        // filteredCartItems: function () {
        //     let cartItems = this.cartItems;
        //     for (let i = 0; i < cartItems.length; i++) {
        //         for (let j = 0; j < cartItems[i].warranty.length; j++) {
        //             cartItems[i].warranty[j].price = cartItems[i].warranty[j].price*cartItems[i].quantity;
        //         }
        //     }
        //     return cartItems;
        // }
    }
})