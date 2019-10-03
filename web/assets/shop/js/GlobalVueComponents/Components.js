Vue.component('flat-pickr', VueFlatpickr);
Vue.component('section-posts-row', {
    props: {
        posts: {
            type: Array,
            default: function () {
                return []
            }
        },
        postskey: {
            type: [Number, String],
            default: 0
        },
        slider: {
            type: Boolean,
            default: false
        }
    },
    data: function () {
        return {
            windowWidth: 0,
        }
    },
    mounted: function () {
        this.windowWidth = $(window).width();
        var that = this;
        $(window).resize(function () {
            that.windowWidth = $(window).width();
        });
    },
    computed: {
        turnSlider: function () {
            if (this.slider && this.windowWidth < 1024) {
                return true;
            } else {
                return false;
            }
        }
    }
})
Vue.component('section-min-header', {
    props: {
        label: {
            type: [String, Number],
            default: 'Title'
        },
        h1: {
            type: Boolean,
            default: false
        }
    }
})
// Vue.component('popup-ret-ref', {
//     props: ['value', 'state'],
//     template: `
//     <a @click="loadComments(loadPagination)" v-if="loadMoreShow" >[[loadmorelabel]]</a>
//     `,
//     data() {
//         return {
//             productData: false,
//             hasReturn: false
//         }
//     },
//     mounted: function () {
//         if (this.value) {
//             this.hasRetRef(this.value);
//             this.productData = this.value;
//         }
//     },
//     watch: {
//         value: function (newVal, oldVal) {
//             if (newVal) {
//                 this.hasRetRef(newVal);
//                 this.productData = newVal;
//             }
//         }
//     },
//     methods: {
//         showPopup: function (data) {
//             if (this.hasReturn) {
//                 $(data.target)
//                     .btpopup({
//                         className: {
//                             loading: 'loading',
//                             btpopup: 'cui btpopup user ret-ref',
//                             position: 'top center',
//                             visible: 'visible'
//                         },
//                         html: '<div class="header grid-x">Returned</div>',
//                         // boundary: '#Orders',
//                         position: 'top center',
//                         // on: 'click',
//                         hideOnScroll: true,
//                         // jitter: 10,
//                         arrowPixelsFromEdge: 30,
//                         // distanceAway: -5,
//                     }).btpopup('show');
//             }
//         },
//         hasRetRef: function (product) {
//             this.hasReturn = product.is_returned;
//         }


//     }
// });
Vue.component('section-swiper', {
    props: {
        posts: {
            type: [Array, Boolean],
            default: false
        },
        id: {
            type: [Boolean, String, Number],
            default: false
        },
        recomended: {
            type: [Array, Boolean],
            default: false
        }
    },
    data: function () {
        return {
            swiperId: '',
            swiper: null,
            parameters: {
                observer: true,
                grabCursor: false,
                watchOverflow: true,
                direction: 'horizontal',
                loop: false,
                roundLengths: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                // Default parameters
                slidesPerView: 4,
                spaceBetween: 30,
                // Responsive breakpoints
                breakpoints: {
                    // when window width is <= 568px
                    568: {
                        slidesPerView: 1,
                        spaceBetween: 10
                    },
                    // when window width is <= 768px
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    // when window width is <= 1024px
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30
                    }
                }
            }
        }
    },
    mounted: function () {
        const self = this;
        this.swiperId = !this.id ? 'swiper_' + (Math.random().toString(36).substr(2, 9)) : this.id;
        this.$nextTick(function () {
            self.swiperCreate();
        });
    },
    computed: {
        filteredRecomended: function () {
            this.recomended.map(key => {
                key.slug = 'products-' + key.slug;
            });
            return this.recomended;
        }
    },
    methods: {
        swiperCreate: function () {
            this.swiper = new Swiper('#' + this.swiperId + '.swiper-container', this.parameters);
        },
        convertRecomendsToArray: function (val) {
            if (isDef(val) && !Array.isArray(val)) {
                let arr = [];
                Object.keys(val).forEach(key => {
                    arr.push(val[key]);
                });
                this.recomended = arr;
            }
        },
        swiperUpdate: function () {
            var that = this;
            if (this.recomended) {
                clearTimeout(sliderTimer);
                sliderTimer = setTimeout(function () {
                    that.swiperCreate(true);
                }, 60);
            }
        }
    },
    watch: {
        recomended: {
            handler(newVal) {
                this.convertRecomendsToArray(newVal);
                this.swiperUpdate();
            },
            deep: true
        },
    }
})