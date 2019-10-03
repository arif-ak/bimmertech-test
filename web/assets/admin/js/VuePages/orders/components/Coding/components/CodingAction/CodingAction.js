var CodingAction = {
    props: ['product', 'i'],
    data: function () {
        return {
            disabled: true,
        }
    },

    created(){
        EventBus.$on("change_status" + this.i, (name)=>{
            this.disabled = false;
        });
    },

    methods: {
        saveStatus: function () {
            this.disabled = true;
            EventBus.$emit("save_status" +this.i);
        }
    }
};