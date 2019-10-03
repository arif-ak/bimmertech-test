var messages = {
    email: function (field) {
        return ("Please enter a valid " + field + " address");
    },
    password: function (field) {
        return ("Please enter your " + field + " ");
    },
    required: function (field) {
        return ("The " + field + " is required.");
    },
    confirmed: function (field) {
        return ("The " + field + " confirmation does not match.");
    },
    is: function (field) {
        return ("The " + field + " confirmation field does not match.");
    },
};
Number.prototype.format = function (n, x) {
    var re = '(\\d)(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$1 ');
};
Number.prototype.evalN = function () {
    return this * 100;
};

const ValidateConfig = {
    aria: true,
    classNames: {
        invalid: 'is-danger'
    },
    classes: true,
    delay: 50,
    dictionary: {
        en: {
            messages: messages
        }
    },
    errorBagName: 'errs', // change if property conflicts
    events: 'blur|change',
    fieldsBagName: 'fields',
    i18n: null, // the vue-i18n plugin instance
    i18nRootKey: 'validations', // the nested key under which the validation messages will be located
    inject: true,
    locale: 'en',
    validity: false,
    mode: 'lazy'
};
Vue.use(VueResource);
Vue.use(VeeValidate, ValidateConfig);
Vue.options.delimiters = ['[[', ']]'];
Vue.filter('price', function (value) {
    if (!value) return '';
    return (value / 100).format(2, 3);
})
Vue.directive('img-loader', {
    // When the bound element is inserted into the DOM...
    inserted: function (el) {
        let isProd = $('meta[name="env"]').attr('content') != 'dev';
        if (isProd) {
            el.src = el.dataset.src;
        } else {
            el.src = '/images/no-image.png';
        }
    }
});
Vue.directive('check-ret-ref', {
    // When the bound element is inserted into the DOM... 
    inserted: function (el, binding, vnode) {
        if (el.dataset.select) {
            $(el).checkbox('check');
        }
        if (binding.value) {
            if (binding.value.product) {
                if (binding.value.product[0]) {
                    if (!binding.value.product[1]) {
                        $(el).checkbox('set disabled');
                    }
                }
                if (binding.value.product[1]) {
                    $(el).checkbox('set enabled');
                }
            }
        }
    },
    componentUpdated: function (el, binding, vnode) {
        if (el.dataset.select) {
            $(el).checkbox('check');
        }
        if (el.dataset.balance) {
            if (el.dataset.balance <= 0) {
                $(el).checkbox('set disabled');
            }
        }
        if (binding.value) {
            if (binding.value.product) {
                if (binding.value.product[0]) {
                    if (!binding.value.product[1]) {
                        $(el).checkbox('set disabled');
                    } else {
                        $(el).checkbox('set enabled');
                    }
                }
                if (binding.value.product[1]) {
                    $(el).checkbox('set enabled');
                }
            }
        }
    }
});
Vue.directive('ret-state', {
    // When the bound element is inserted into the DOM...
    inserted: function (el, binding, vnode) {
        if (binding.value.order_item_unit_return.length > 0) {
            el.classList.toggle('returned', true);

        } else if (el.classList.contains('returned')) {
            el.classList.toggle('returned', false);
        }
    },
    componentUpdated: function (el, binding, vnode) {
        if (binding.value.order_item_unit_return.length > 0) {
            el.classList.toggle('returned', true);
        } else if (el.classList.contains('returned')) {
            el.classList.toggle('returned', false);
        }
    }
});
Vue.directive('return', {
    // When the bound element is inserted into the DOM...
    inserted: function (el, binding, vnode) {
        if (el.dataset.returned) {
            $(el).checkbox('set disabled');
        } else {
            $(el).checkbox('set enabled');
        }
    },
    componentUpdated: function (el, binding, vnode) {
        if (el.dataset.returned) {
            $(el).checkbox('set disabled');
        } else {
            $(el).checkbox('set enabled');
        }
    }
});
const vValidator = new VeeValidate.Validator();
const filterFalsy = arr => arr.filter(Boolean);
const flatten = (arr, depth = 1) =>
    arr.reduce((a, v) => a.concat(depth > 1 && Array.isArray(v) ? flatten(v, depth - 1) : v), []);
Vue.directive('d-down', function (el, binding) {
    if (binding.value.product) {
        let result = flatten(filterFalsy(binding.value.product));
        if (result.length > 0) {
            let arr = [];
            result.forEach(elem => {
                if (elem.title) {
                    delete elem.price;
                }
                let name = (elem.name) ? elem.name : (elem.title) ? elem.title : '',
                    price = (elem.price) ? (elem.price / 100) : elem.price;
                if (isDef(price)) {
                    price = ' $' + price.format(2, 3);
                    arr.push(name.concat(price));
                } else {
                    arr.push(name);
                }
            });
            el.innerHTML = arr.join(', '); //console.log(arr.join(', '));
        }
    }
});
Vue.filter('date',
    function (val) {
        return formatDate(parseDate(val));
    });
const EventBus = new Vue();
let controller;
let signal;
// super simple pt-BR date parser
function parseDate(str) {
    if (str === null || isDate(str)) return str || null;
    var p = str.match(/^(\d{1,2})\/?(\d{1,2})?\/?(\d{2,4})?$/);
    if (!p) return null;
    return new Date(parseInt(p[3] || new Date().getFullYear()), parseInt(p[2] || (new Date().getMonth() + 1)) - 1, parseInt(p[1]), 0, 0, 0, 0);
}
// super simple pt-BR date format
function formatDate(dt) {
    if (dt == null) return '';
    var f = function (d) {
        return d < 10 ? '0' + d : d;
    };
    return f(dt.getDate()) + '/' + f(dt.getMonth() + 1) + '/' + dt.getFullYear().toString().slice(-2);
}
// is object a date?
function isDate(d) {
    return Object.prototype.toString.call(d) === '[object Date]';
}
const isOk = response =>
    response.redirected ? response :
    (!response.redirected && response.ok) ? response.json() :
    (response.status === 404) ? response.statusText :
    Promise.reject(new Error('Failed to load data from server'));
const headers = new Headers({
    'Content-Type': 'application/json'
});

function status(response) {
    if (response.status >= 200 && response.status < 302) {
        return Promise.resolve(response);
    } else {
        return Promise.reject(new Error(response.statusText))
    }
}

function json(response) {
    return response.clone().json().catch(function () {
        if (response.redirected) {
            return;
        }
    });
}

function postRequest(url, data) {
    url = '/admin/api2/' + url;
    return fetch(url, {
        cache: 'no-cache',
        credentials: 'same-origin', // 'include', default: 'omit'
        method: 'POST', // 'GET', 'PUT', 'DELETE', etc.
        body: JSON.stringify(data), // Coordinate the body type with 'Content-Type'
        headers: headers
    }).then(status).then(json);
}

function putRequest(url, data) {
    url = '/admin/api2/' + url;
    return fetch(url, {
        credentials: 'same-origin', // 'include', default: 'omit'
        method: 'PUT', // 'GET', 'PUT', 'DELETE', etc.
        body: JSON.stringify(data), // Coordinate the body type with 'Content-Type'
        headers: new Headers({
            'Content-Type': 'application/json'
        }),
    }).then(status).then(json);
}

function getRequest(url) {
    url = '/admin/api2/' + url;
    return fetch(url, {
        credentials: 'same-origin', // 'include', default: 'omit'
        method: 'GET', // 'GET', 'PUT', 'DELETE', etc.
        redirect: 'follow',
        headers: headers
    }).then(status).then(json);
}

function getRequestWithAbort(url, data) {
    if (controller !== undefined) {
        // Cancel the previous request
        controller.abort();
    }
    if (!data.signal) {
        // Feature detect
        if ("AbortController" in window) {
            controller = new AbortController;
            signal = controller.signal;
        }
    }
    url = '/admin/api2/' + url;
    return fetch(url, {
        credentials: 'same-origin', // 'include', default: 'omit'
        method: 'GET', // 'GET', 'PUT', 'DELETE', etc.
        redirect: 'follow',
        headers: headers,
        signal
    }).then(status).then(json);
}

function deleteRequest(url) {
    return fetch(url, {
        credentials: 'same-origin', // 'include', default: 'omit'
        method: 'DELETE', // 'GET', 'PUT', 'DELETE', etc.
        keepalive: false, // 'GET', 'PUT', 'DELETE', etc.
        headers: new Headers({
            'Content-Type': 'application/json'
        })
    })
}

var backgroundCropper = new Vue({
    el: '#backgroundCropper',
    data: function () {
        return {
            cropper: "",
            selectedFile: "",
            imageSrc: "",
            newImageSrc: "",
            EventName: '',
            inpId: '',

            inputIndex: false,
            show: false,
            aspectRatio: NaN,

            aspectRatios: [{
                    name: 'Custom',
                    aspectRatio: NaN
                },
                {
                    name: '4 / 3',
                    aspectRatio: 4 / 3
                },
                {
                    name: '16 / 9',
                    aspectRatio: 16 / 9
                },
                {
                    name: '1 / 1',
                    aspectRatio: 1 / 1
                }
            ],
            buttons: false
        }
    },
    mounted: function () {
        EventBus.$on("cropperFile", data => {
            if (data.aspectRatio == 'custom') {
                this.buttons = true;
                this.aspectRatio = NaN
            } else {
                this.aspectRatio = data.aspectRatio;
                this.buttons = false;
            }
            this.selectedFile = data.file;
            this.imageSrc = window.URL.createObjectURL(data.file);
            this.inputIndex = data.m
            this.EventName = data.EventName
            this.show = true;
            this.inpId = data.inpId;
            // document.getElementById(this.inpId).value = "";
        });
    },
    watch: {
        imageSrc: function () {
            var that = this;
            let image = document.getElementById("newImage");
            if (that.cropper) {
                that.cropper.destroy();
            }
            setTimeout(function () {
                that.cropper = new Cropper(image, {
                    aspectRatio: that.aspectRatio
                });
            }, 10);
        },
        aspectRatio: function () {
            var that = this;
            if (this.cropper) {
                let image = document.getElementById("newImage");
                setTimeout(function () {
                    that.cropper.destroy();
                    that.cropper = new Cropper(image, {
                        aspectRatio: that.aspectRatio
                    });
                }, 10);
            }
        }
    },
    methods: {
        hide: function () {
            this.show = false;
            this.cropper.destroy();
            document.getElementById(this.inpId).value = "";
            this.aspectRatio = NaN;
        },
        crop: function () {
            var that = this;
            that.cropper.crop();

            EventBus.$emit(that.EventName, {
                newImageSrc: that.cropper.getCroppedCanvas().toDataURL(),
                newImage: that.cropper.getCroppedCanvas(),
                m: that.inputIndex,
                inpId: that.inpId
            });

            that.hide();
        }
    }
});




// https://github.com/werk85/fetch-intercept
/* const fetchIntercept = function (r) {
    function n(e) {
        if (t[e]) return t[e].exports;
        var o = t[e] = {
            exports: {},
            id: e,
            loaded: !1
        };
        return r[e].call(o.exports, o, o.exports, n), o.loaded = !0, o.exports
    }
    var t = {};
    return n.m = r, n.c = t, n.p = "", n(0)
}([function (r, n, t) {
    (function (n, e) {
        "use strict";

        function o(r) {
            if (Array.isArray(r)) {
                for (var n = 0, t = Array(r.length); n < r.length; n++) t[n] = r[n];
                return t
            }
            return Array.from(r)
        }

        function i(r) {
            if (!r.fetch) try {
                t(2)
            } catch (n) {
                throw Error("No fetch avaibale. Unable to register fetch-intercept")
            }
            r.fetch = function (r) {
                return function () {
                    for (var n = arguments.length, t = Array(n), e = 0; n > e; e++) t[e] = arguments[e];
                    return f.apply(void 0, [r].concat(t))
                }
            }(r.fetch)
        }

        function f(r) {
            for (var n = arguments.length, t = Array(n > 1 ? n - 1 : 0), e = 1; n > e; e++) t[e - 1] = arguments[e];
            var i = l.reduce(function (r, n) {
                    return [n].concat(r)
                }, []),
                f = Promise.resolve(t);
            return i.forEach(function (r) {
                var n = r.request,
                    t = r.requestError;
                (n || t) && (f = f.then(function (r) {
                    return n.apply(void 0, o(r))
                }, t))
            }), f = f.then(function (n) {
                return r.apply(void 0, o(n))
            }), i.forEach(function (r) {
                var n = r.response,
                    t = r.responseError;
                (n || t) && (f = f.then(n, t))
            }), f
        }
        var u = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (r) {
                return typeof r
            } : function (r) {
                return r && "function" == typeof Symbol && r.constructor === Symbol ? "symbol" : typeof r
            },
            c = "object" === ("undefined" == typeof navigator ? "undefined" : u(navigator)) && "ReactNative" === navigator.product,
            s = "object" === ("undefined" == typeof n ? "undefined" : u(n)) && !0,
            a = "object" === ("undefined" == typeof window ? "undefined" : u(window)),
            p = "function" == typeof importScripts;
        if (c) i(e);
        else if (p) i(self);
        else if (a) i(window);
        else {
            if (!s) throw new Error("Unsupported environment for fetch-intercept");
            i(e)
        }
        var l = [];
        r.exports = {
            register: function (r) {
                return l.push(r),
                    function () {
                        var n = l.indexOf(r);
                        n >= 0 && l.splice(n, 1)
                    }
            },
            clear: function () {
                l = []
            }
        }
    }).call(n, t(1), function () {
        return this
    }())
}, function (r, n) {
    "use strict";

    function t() {
        s = !1, f.length ? c = f.concat(c) : a = -1, c.length && e()
    }

    function e() {
        if (!s) {
            var r = setTimeout(t);
            s = !0;
            for (var n = c.length; n;) {
                for (f = c, c = []; ++a < n;) f && f[a].run();
                a = -1, n = c.length
            }
            f = null, s = !1, clearTimeout(r)
        }
    }

    function o(r, n) {
        this.fun = r, this.array = n
    }

    function i() {}
    var f, u = r.exports = {},
        c = [],
        s = !1,
        a = -1;
    u.nextTick = function (r) {
        var n = new Array(arguments.length - 1);
        if (arguments.length > 1)
            for (var t = 1; t < arguments.length; t++) n[t - 1] = arguments[t];
        c.push(new o(r, n)), 1 !== c.length || s || setTimeout(e, 0)
    }, o.prototype.run = function () {
        this.fun.apply(null, this.array)
    }, u.title = "browser", u.browser = !0, u.env = {}, u.argv = [], u.version = "", u.versions = {}, u.on = i, u.addListener = i, u.once = i, u.off = i, u.removeListener = i, u.removeAllListeners = i, u.emit = i, u.binding = function (r) {
        throw new Error("process.binding is not supported")
    }, u.cwd = function () {
        return "/"
    }, u.chdir = function (r) {
        throw new Error("process.chdir is not supported")
    }, u.umask = function () {
        return 0
    }
}, function (r, n) {
    r.exports = require("whatwg-fetch")
}]);

///// Code

fetchIntercept.register({

    request: function (url, config) {
        console.log(url);
        return [url, config];
    },

    requestError: function (error) {
        // console.log(error);
        return Promise.reject(error);
    },

    response: function (response) {
        // console.log(response);
        return response;
    },

    responseError: function (error) {
        // console.log(error);
        return Promise.reject(error);
    }
}); */