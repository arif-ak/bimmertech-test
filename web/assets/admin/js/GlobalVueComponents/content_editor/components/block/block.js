var Block = {
    props: {
        value: {
            default: {
                header: '',
                subHeader: '',
                content: '',
                position: 'center',
                image: '',
                video: ''
            }
        },
    },
    data: function () {
        var that = this;
        return {
            content: {},
            myModel: '<p><span style="color: #ff0000;">Initial Value</span></p>',
            positions: [
                {
                    val: 'left',
                    label: 'Left'
                },
                {
                    val: 'center',
                    label: 'Center'
                },
                {
                    val: 'right',
                    label: 'Right'
                }
            ],

            imageOrVideo: 'image'
        }
    },
    components: {
        'tinymce': VueEasyTinyMCE
    },
    mounted: function () {
        this.content = this.value
    },
    watch: {
        value: function () {
            this.content = this.value
        }
    },
    updated: function () {
        this.$emit('input', this.content)
        this.$emit('change', this.content)
    },
    methods: {
        deleteBlock: function () {
            this.$emit('delete')
        }
    }
}