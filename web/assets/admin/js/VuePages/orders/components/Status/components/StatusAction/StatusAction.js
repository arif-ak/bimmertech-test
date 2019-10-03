var StatusAction = {
    props: ['status', 'initialStatus', 'i', 'boardTitles', 'access'],
    data: function () {
        return {
            disabled: false,
            show: true
        }
    },

    created(){
        // EventBus.$on("change_status" + this.i, (name)=>{
        //     this.disabled = false;
        // });
        //console.log(this.status);
    },

    methods: {
        saveStatus: function () {
            // selectedStatus, statusType
            this.disabled = false;
            this.show = true;

            this.$emit("save-status", {
                selectedStatus: this.status.selectedStatus.value,
                initialStatuses: this.initialStatus.selectedStatus.value,
                statusType: this.status.statusType
            });
        },

        editStatus: function () {
            this.show = false;
            this.disabled = true;
        },

        onChange: function() {
            this.disabled = false;
        }
    }
};