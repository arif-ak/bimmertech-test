var contactPage = new Vue ({
    el: '#contact_us',
    methods: {
        func: function () {
            $('body .Branding_body .logo_container .Search input:submit')
        },
        openModalAskExpert: function (tab) {
            EventBus.$emit('selectModalAskExpertTab', tab)
        }  
    }
})