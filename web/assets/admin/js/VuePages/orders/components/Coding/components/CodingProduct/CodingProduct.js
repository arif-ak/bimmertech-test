var CodingProduct = {
    props: ['product', 'i'],
    data: function () {

        return {
            status: false,
            show: false,
            showSelect: false,
            statuses: [
                {
                    value: "not coded"
                },
                {
                    value: "completed"
                },

            ],
            selectedStatus: false
        }
    },
    watch: {
        product: {
            handler(newVal){
                this.selectedStatus = this.product.coding.status
            },
            deep: true
        },
    },
    created(){
        var that = this;
        this.selectedStatus = this.product.coding.status;
        EventBus.$on("save_status" + this.i, (product)=>{
            that.show = false;
            that.save();
        });
    },

    methods: {
        save: function () {
            var that = this;
            var data;
            if (this.product.coding.drop_down_option) {
                data = {
                    "drop_down_id" : this.product.id,
                    "status": this.selectedStatus
                }
            } else {
                data = {
                    "order_item_id" : this.product.id,
                    "status": this.selectedStatus
                }
            }
            this.$emit('loading');
            this.$http.post('/admin/api2/order/' + orderId + '/edit-codding', data)
                .then(response => {
                    that.$emit('success-message', 'Coding has been successfully updated.');
                    that.$emit('update');
                    EventBus.$emit('load_order_status');
                }).catch(error => {
            });
        },
        onChange: function () {
            EventBus.$emit("change_status" + this.i, this.product);
            this.show = true
        }
    }
    // watch: {
    //   radio: function () {
    //       this.componentRadio = this.radio;
    //   }
    // },
    // methods: {
    //     changeRadio: function () {
    //         this.$emit('change', this.componentRadio)
    //     }
    // }
};