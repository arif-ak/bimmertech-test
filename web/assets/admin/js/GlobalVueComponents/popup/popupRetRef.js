Vue.component('popup-ret-ref', {
    props: ['value', 'access'],
    template: `
    <span @click="showPopup($event)" class="bt-icons-wrapper">
        <span class="bt-returned bt-icon" v-if="hasReturn"></span>
        <span class="bt-refunded bt-icon" v-if="hasRefund"></span>
    </span>
    `,
    data() {
        return {
            productData: false,
            hasRefund: false,
            hasReturn: false
        }
    },
    mounted: function () {
        if (this.value) {
            this.hasRetRef(this.value);
            this.productData = this.value;
        }
    },
    watch: {
        value: function (newVal, oldVal) {
            if (newVal) {
                this.hasRetRef(newVal);
                this.productData = newVal;
            }
        }
    },
    methods: {
        showPopup: function (data) {
            if (this.hasRefund || this.hasReturn) {
                let dataHtml = this.parseRetRef(this.value),
                    dataAccess = (this.access.order_refund_edit) ? this.access.order_refund_edit : this.access;
                $(data.target)
                    .btpopup({
                        className: {
                            loading: 'loading',
                            btpopup: 'cui btpopup ret-ref',
                            position: 'top center',
                            visible: 'visible'
                        },
                        html: this.pHtml(dataHtml, dataAccess),
                        boundary: '#orders-page',
                        position: 'top left',
                        on: 'click',
                        hideOnScroll: true,
                        jitter: 10,
                        arrowPixelsFromEdge: 30,
                        distanceAway: -5,
                    }).btpopup('show');
            }
        },
        parseRetRef: function (data) {
            let map = new Map();
            let result = {
                title: {},
                content: []
            };
            if (this.hasReturn) {
                map.set(data.order_item_unit_return[0].id, data.order_item_unit_return[0]);
                result.title.ret = 'Returned';
                result.content.push(data.order_item_unit_return[0]);
            }
            if (this.hasRefund) {
                result.title.ref = 'Refunded';
                data.order_item_unit_refund.forEach(element => {
                    if (!map.has(element.id)) {
                        map.set(element.id, element);
                        result.content.push(element);
                    }
                });
            }
            return result;
        },
        hasRetRef: function (product) {
            this.hasRefund = product.order_item_unit_refund.length > 0;
            this.hasReturn = product.order_item_unit_return.length > 0;
        },
        pHtml: function (data, access = null) {
            var html = '';
            if (isDef(data)) {
                if (isDef(data.title) && data.title) {
                    html += this.genElem(data.title, 'title');
                }
                if (isDef(data.content) && data.content) {
                    html += this.genElem(data.content, 'reason', access);
                }
                if (isDef(data.link) && data.link) {
                    html += '<a target="_blank" class="link" href="' + data.link + '">' + data.link + '</a>';
                }
            }
            return html;
        },
        genElem: function (items, option, access = null) {
            var html = '';
            switch (option) {
                case 'title':
                    html += '<div class="header grid-x">';
                    for (let key in items) {
                        if (items[key]) {
                            let cls = convertToKebabCase(items[key]);
                            html += '<div class="' + cls + '">' + items[key] + '</div>';
                        }
                    }
                    html += '</div>';
                    break;
                case 'reason':
                    html += '<div class="content">';
                    for (let key in items) {
                        html += '<div class="grid-x align-justify align-middle message">' +
                            '<div class="cell large-auto text">' + items[key].comment + '</div>' +
                            '<div class="cell shrink">';
                        if (access && access === true) {
                            html += '<button onclick="emitMoodal(' + items[key].id + ')" class="ui mini button summaryEditBtn yellow" id="' + items[key].id + '">Edit</button>';
                        } else {
                            html += '<button onclick="emitMoodal(' + items[key].id + ')" class="ui mini button summaryEditBtn yellow disabled" id="' + items[key].id + '">Edit</button>';
                        }
                        html += '</div>' +
                            '</div>';
                    }
                    html += '</div>';
                    break;

                default:
                    break;
            }
            return html;
        },

    }
});