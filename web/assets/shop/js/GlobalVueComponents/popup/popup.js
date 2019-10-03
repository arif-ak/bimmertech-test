Vue.component('section-popup', {
    props: {
        idbutton: {
            type: [Boolean, String, Number],
            default: false
        },
        idpopup: {
            type: [Boolean, String, Number],
            default: false
        },
        on: {
            type: [Boolean, String, Number],
            default: false
        },
        position: {
            type: [Boolean, String, Number],
            default: false
        },
        transition: {
            type: [Boolean, String, Number],
            default: false
        },
        duration: {
            type: [Boolean, String, Number],
            default: false
        },
        target: {
            type: [Boolean, String, Number],
            default: false
        },
        divider: {
            type: [Boolean, String, Number],
            default: false
        },
        menuToggle: {
            type: [Boolean, String],
            default: false
        },
        distance: {
            type: [Boolean, String, Number],
            default: false
        },
        props: {
            type: [Boolean, String, Number],
            default: false
        },
        boundary: {
            type: [Boolean, String],
            default: window
        },
        jitter: {
            type: Number,
            default: 2
        },
        offset: {
            type: Number,
            default: 0
        },
        edge: {
            type: Number,
            default: 0
        },
        hideOnScroll: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            buttonId: '',
            popupId: '',
            header: false,
            options: {
                popup: '.reviewsArrowPopup',
                inline: true,
                hoverable: false,
                on: 'hover',
                position: 'top center',
                transition: 'fade',
                duration: 100,
                arrowPixelsFromEdge: 20,
                target: false,
                distanceAway: 0,
                jitter: 2,
                offset: 0,
                boundary: '',
                debug: false,
                onVisible: function () {
                    let t = this[0],
                        list = t.classList,
                        calc = (t.previousElementSibling.offsetLeft + (t.previousElementSibling.clientWidth / 4)) + 1,
                        pseudo = document.getElementById(t.id);
                    if (list.contains('bottom') && list.contains('left')) {
                        if (!list.contains('pseudoStyle_' + t.id)) {
                            pseudo.pseudoStyle("before", "left:" + calc.toFixed(0) + "px !important; opacity: 1 !important;", t.id);
                        }
                    }
                    if (!list.contains('pseudoStyle_' + t.id)) {
                        pseudo.pseudoStyle("before", "opacity: 1 !important;", t.id);
                    }
                },
                onHidden: function () {},
                onHide: function () {},
                onShow: function () {}
            },
            propsData: null
        }
    },
    created: function () {
        this.propsData = this.props;
    },
    mounted: function () {
        var that = this;
        this.buttonId = !this.idbutton ? 'bt-popup_' + (Math.random().toString(36).substr(2, 9)) : this.idbutton;
        this.popupId = !this.idpopup ? 'popup_' + (Math.random().toString(36).substr(2, 9)) : this.idpopup;

        this.options.popup = '#' + this.popupId;
        if (!this.on) {
            this.options.hoverable = false;
        } else if (this.on === 'hoverable') {
            this.options.hoverable = true;
        } else {
            this.options.on = this.on;
        }
        
        this.options.position = this.position ? this.position : this.options.position;
        this.options.boundary = this.boundary ? this.boundary : this.options.boundary;
        this.options.offset = this.offset ? +this.offset : this.options.offset;
        this.options.transition = this.transition ? this.transition : this.options.transition;
        this.options.duration = this.duration ? this.duration : this.options.duration;
        this.options.target = this.target ? this.target : this.options.target;
        this.options.jitter = this.jitter ? +this.jitter : this.options.jitter;
        this.options.distanceAway = this.distance ? +this.distance : this.options.distanceAway;
        this.options.arrowPixelsFromEdge = this.edge ? +this.edge : this.options.arrowPixelsFromEdge;

        if (this.divider) {
            var that = this;
            this.options.onShow = function () {
                auditOnVisible = false;
                EventBus.$emit('divider', that.divider);
                setTimeout(() => {
                    auditOnVisible = true;
                }, 20);
            };
            this.options.onHide = function () {
                if (auditOnVisible) {
                    EventBus.$emit('divider', false);
                }
            };
        }
        if (this.hideOnScroll) {
            $(document).scroll(function () {
                $('#' + that.buttonId).popup('hide');
            });
        }
        this.popup();
    },
    updated: function () {
        // this.popup();
    },
    watch: {
        props: {
            handler(newVal) {
                this.propsData = newVal;
            },
            deep: true
        }
    },
    methods: {
        popup: function () {
            setTimeout(() => {
                // console.log('$("#'+this.buttonId+'").popup()');
                $('#' + this.buttonId).popup(this.options);
            }, 1000);
        }
    }
});