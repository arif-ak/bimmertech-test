Number.prototype.format = function (n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));
    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};
HTMLElement.prototype.pseudoStyle = function (element, cssRules, id) {
    var _this = this;
    var _sheetId = "pseudoStyles";
    var _head = document.head || document.getElementsByTagName('head')[0];
    var _sheet = document.getElementById(_sheetId) || document.createElement('style');
    _sheet.id = _sheetId;
    var className = "pseudoStyle_" + id;
    if (!_this.classList.contains(className)) {
        _this.className += " " + className;
    }
    _sheet.innerHTML += "\n." + className + ":" + element + "{" + cssRules + "}";
    _head.appendChild(_sheet);
    return this;
};

var sliderTimer;
var testSlider;
var testMap;
var testSet;

const breakpoints = {
    Mobile: 'screen and (max-width: 39.9375em)',
    Tablet: 'screen and (min-width: 40em) and (max-width: 63.9375em)',
    Large: 'screen and (min-width: 64em)'
};

function isProd() {
    return $('meta[name="env"]').attr('content') != 'dev';
}

const isEmptyArray = arr => {
    return Array.isArray(arr) && arr.length === 0;
};

const eventBusLoop = (arr) => {
    let matcher = null;
    matcher = arr.map(key => key.split(':'));
    return matcher.forEach(e => {
        if (isDef(e[1])) {

            return EventBus.$emit(e[0], stringToBoolean(e[1]));
        } else {
            return EventBus.$emit(e[0]);
        }
    });
};

const getBrowserWidth = () => {
    Object.keys(breakpoints).forEach(k => {
        if (window.matchMedia(breakpoints[k]).matches) {
            result = k;
        }
    });
    return result.toLowerCase();
};

function stringToBoolean(string) {
    switch (string.toLowerCase().trim()) {
        case "true":
        case "yes":
        case "1":
            return true;
        case "false":
        case "no":
        case "0":
        case null:
            return false;
        default:
            return string;
    }
}
const convertToSnakeCase = (str) => {
    if (isUndef(str)) {
        return;
    }
    return str
        .replace(/([a-z])([A-Z])/g, '$1-$2') // get all lowercase letters that are near to uppercase ones
        .replace(/[\s_]+/g, '_') // replace all spaces and low dash
        .toLowerCase(); // convert to lower case
};


const checker = (obj) => {
    let objSet = new Set();
    if (isObject(obj)) {
        Object.keys(obj).forEach(key => {
            if (!!obj[key]) {
                objSet.add(key);
            }
        });
    }
    return objSet;
};

const urlRE = new RegExp(/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/gi);

function getArrayById(array, id) {
    return array.find(x => x.id === id);
}

function isObject(obj) {
    return (typeof obj === "object" && obj !== null) || typeof obj === "function";
}

function isUndef(v) {
    return v === undefined || v === null;
}

function isDef(v) {
    return v !== undefined && v !== null;
}

/**
 * Mix properties into target object.
 */
function extend(to, _from) {
    for (var key in _from) {
        to[key] = _from[key];
    }
    return to;
}

/**
 * Merge an Array of Objects into a single Object.
 */
function toObject(arr) {
    var res = {};
    for (var i = 0; i < arr.length; i++) {
        if (arr[i]) {
            extend(res, arr[i]);
        }
    }
    return res;
}

/**
 * Get the raw type string of a value, e.g., [object Object].
 */
var _toString = Object.prototype.toString;

function toRawType(value) {
    return _toString.call(value).slice(8, -1)
}

/**
 * Strict object type check. Only returns true
 * for plain JavaScript objects.
 */
function isPlainObject(obj) {
    return _toString.call(obj) === '[object Object]'
}

function isRegExp(v) {
    return _toString.call(v) === '[object RegExp]'
}

/**
 * Convert a value to a string that is actually rendered.
 */
function toString(val) {
    return val == null ?
        '' :
        Array.isArray(val) || (isPlainObject(val) && val.toString === _toString) ?
        JSON.stringify(val, null, 2) :
        String(val)
}

const deepParseInt = input => {
    if (typeof input === 'number') return input;

    if (typeof input === 'string') return parseInt(input);

    const map = {};
    for (const element in input) {
        map[element] = parseInt(input[element]);
    }

    return map;
};

const none = (arr, fn = Boolean) => !arr.some(fn);
const reducedFilter = (data, keys, fn) =>
    data.filter(fn).map(el =>
        keys.reduce((acc, key) => {
            acc[key] = el[key];
            return acc;
        }, {})
    );

const removeFromArray = (arr, func) =>
    Array.isArray(arr) ?
    arr.filter(func).reduce((acc, val) => {
        arr.splice(arr.indexOf(val), 1);
        return acc.concat(val);
    }, []) : [];


const convertToKebabCase = (str) => {
    return str
        .replace(/([a-z])([A-Z])/g, '$1-$2') // get all lowercase letters that are near to uppercase ones
        .replace(/[\s_]+/g, '-') // replace all spaces and low dash
        .toLowerCase(); // convert to lower case
};
const difference = (a, b) => {
    const s = new Set(b);
    return a.filter(x => !s.has(x));
};