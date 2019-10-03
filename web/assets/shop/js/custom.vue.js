//import Vue from 'vue';
//import axios from 'axios'
//import VeeValidate from 'vee-validate';
var messages = {
  email: function (field) {
    return ("Please enter a valid " + field + " address");
  },
  password: function (field) {
    return ("Please enter your " + field + " ");
  },
  required: function (field) {
    return ("The " + field + " field is required.");
  },
  confirmed: function (field) {
    return ("The " + field + " confirmation does not match.");
  },
  is: function (field) {
    return ("The " + field + " confirmation field does not match.");
  },
};
var isMobile = false; //initiate as false
// device detection
if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) ||
  /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
  isMobile = true;
}
Number.prototype.format = function (n, x) {
  var re = '(\\d)(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
  return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$1 ');
};
const ValidateConfig = {
  aria: true,
  classNames: {
    invalid: 'is-danger'
  },
  classes: true,
  delay: 0,
  dictionary: {
    en: {
      messages: messages

    }
  },
  errorBagName: 'errors', // change if property conflicts
  events: 'input|blur',
  fieldsBagName: 'fields',
  i18n: null, // the vue-i18n plugin instance
  i18nRootKey: 'validations', // the nested key under which the validation messages will be located
  inject: true,
  locale: 'en',
  strict: true,
  validity: false,
};

Vue.use(VueSessionStorage);
Vue.use(VueResource);
Vue.use(VeeValidate, ValidateConfig);
Vue.directive('img-loader', {
  // When the bound element is inserted into the DOM...
  inserted: function (el) {
    if (isProd()) {
      el.src = el.dataset.src;
    } else if (!!imageConfig.productListImg) {
      el.src = el.dataset.src;
    } else {
      el.src = '/images/270x270.png';
      el.style.cssText += 'max-height: 220px;';
    }
  }
});
Vue.directive('bt-card-hover', {
  // When the bound element is inserted into the DOM...
  inserted: function (el, binding, vnode) {},
  componentUpdated: function (el, binding, vnode) {
    $('.bt-card .image.dimmable').dimmer({
      on: 'hover'
    });
  }
});
Vue.directive('check-env', {
  // When the bound element is inserted into the DOM...
  inserted: function (el, binding, vnode) {
    console.log('inserted: ', el, binding, vnode);
  },
  update: function (el, binding, vnode) {
    console.log('update: ', el, binding, vnode);  
  },
  componentUpdated: function (el, binding, vnode) {
    console.log('el, binding, vnode: ', el, binding, vnode);
  }
});
Vue.filter('price', function (value) {
  if (!value) return '';
  return (value / 100).format(2, 3);
});
Vue.options.delimiters = ['[[', ']]'];


//axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
/* axios.defaults.headers.post['Content-Type'] =
'application/x-www-form-urlencoded'; */
/* Vue.http.options.emulateJSON = true;
Vue.http.options.emulateHTTP = true; */
// register the grid component

const EventBus = new Vue();

const emailRE = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

const MainData = new Vue({
  data: {
    vin: '',
  },
  created: function () {
    this.setVin();
    //this.getAll();


  },
  methods: {
    getVin: function () {
      return this.$session.get('vin');
      //this.$session.set('vin', $('meta[name="vin"]').attr('title'));
    },
    getScrollEvent: function (name) {
      return this.$session.get(name);
      //this.$session.set('vin', $('meta[name="vin"]').attr('title'));
    },
    getScrollEventState: function () {
      return this.$session.get('emitScroll');
      //this.$session.set('vin', $('meta[name="vin"]').attr('title'));
    },
    setVin: function (vinNumber) {
      this.vin = vinNumber;
      this.$session.set('vin', vinNumber);
    },
    setScrollEvent: function (name, uri) {
      this.$session.set(name, uri);
    },
    setScrollEventState: function (option) {
      this.$session.set('emitScroll', option);
    }

  }
});

let controller,
  signal;
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

  url = '/api2/' + url;
  return fetch(url, {
    cache: 'no-cache',
    credentials: 'same-origin', // 'include', default: 'omit'
    method: 'POST', // 'GET', 'PUT', 'DELETE', etc.
    body: JSON.stringify(data), // Coordinate the body type with 'Content-Type'
    headers: headers,
    signal
  }).then(status).then(json);
}

function putRequest(url, data) {
  return fetch(url, {
    cache: 'no-cache',
    credentials: 'same-origin', // 'include', default: 'omit'
    method: 'PUT', // 'GET', 'PUT', 'DELETE', etc.
    body: JSON.stringify(data), // Coordinate the body type with 'Content-Type'
    headers: headers,
  }).then(status).then(json);
}

function getRequest(url) {
  if (url != '/vincheck/clear') {
    url = '/api2/' + url;
  }
  return fetch(url, {
    cache: 'no-cache',
    method: 'GET', // 'GET', 'PUT', 'DELETE', etc.
    headers: headers,
  }).then(status).then(json);
}

function deleteRequest(url) {
  url = '/api2/' + url;
  return fetch(url, {
    cache: 'no-cache',
    method: 'DELETE', // 'GET', 'PUT', 'DELETE', etc.
    keepalive: false, // 'GET', 'PUT', 'DELETE', etc.
    headers: headers
  });
}

function sendMailG(page, data) {
  const baseURL = 'https://mail.google.com/mail/u/0/?view=cm&fs=1';
  let _winURL,
    _mailto = data.mailto ? data.mailto : '',
    _title = data.title ? data.title : '',
    _body = data.body ? data.body : '';
  _title = _title.replace(/\s/g, '+');
  _body = _body.replace(/\s/g, '+');
  switch (true) {
    case (page == 'product'):
      _winURL = baseURL + "&to&su=" + _title + "&body=" + _body;
      break;
    case (page == 'main'):
      _winURL = baseURL + "&to=" + _mailto + "&su=" + _title;
      break;
  }
  var win = window.open(_winURL + "&ui=2&tf=1", "MsgWindow", "width=1000,height=500");
  win.focus();
}
var mainStorage = {

  fetch: function () {
    var cookie = JSON.parse(localStorage.getItem('cookieAccepted'));
    if (cookie === null) {
      cookie = false;
    }
    return cookie;
  },
  save: function (cookie) {
    localStorage.setItem('cookieAccepted', JSON.stringify(cookie));
  }
};
//import axios from 'axios'

var PayPalWindow;

var payModal = new Vue({
  el: '#payModal',
  methods: {
    showWindow: function () {
      PayPalWindow.focus();
    }
  }
});

var sliderBtnComponent = new Vue({
  el: '#sliderComponent',
  methods: {
    addToCartSlider: function (productId) {
      EventBus.$emit('addProductForCartWithAddons', {
        productId: false,
        variantId: productId,
        warranty: false,
        addons: [],
        savePrice: false,
        dropDown: []
      });
    }
  }
});

function pHtml(data) {
  var html = '';
  if (typeof data !== undefined) {
    if (typeof data.title !== undefined && data.title) {
      html += '<div class="header">' + data.title + '</div>';
    }
    if (typeof data.content !== undefined && data.content) {
      html += '<div class="content">' + data.content + '</div>';
    }
    if (typeof data.link !== undefined && data.link) {
      html += '<a target="_blank" class="link" href="' + data.link + '">' + data.link + '</a>';
    }
  }
  return html;
}

const vValidator = new VeeValidate.Validator();
store.set('CarModels',
  [{
    "name": "1 Series Convertible E88 (2006-2013)",
    "model": "e88",
    "year": "2010"
  }, {
    "name": "1 Series Coupé E82 (2006-2013)",
    "model": "e82",
    "year": "2010"
  }, {
    "name": "1 Series 3-door Hatchback E81 (2006-2011)",
    "model": "e81",
    "year": "2010"
  }, {
    "name": "1 Series 5-door Hatchback E87 (2006-2011)",
    "model": "e87",
    "year": "2010"
  }, {
    "name": "1 Series 5-door Hatchback F20 (2010-2018)",
    "model": "f26",
    "year": "2010"
  }, {
    "name": "1 Series 3 - door Hatchback F21(2010 - 2018)",
    "model": "f21",
    "year": "2010"
  }, {
    "name": "1 Series Sedan F52 (2015-2018)",
    "model": "f52",
    "year": "2015"
  }, {
    "name": "2 Series Active tourer F45 (2013-2018)",
    "model": "f45",
    "year": "2015"
  }, {
    "name": "2 Series Convertible F23 (2014-2018)",
    "model": "f23",
    "year": "2015"
  }, {
    "name": "2 Series Coupe F22 (2012-2018)",
    "model": "f22",
    "year": "2015"
  }, {
    "name": "2 Series Coupe M2 F87 (2014-2018)",
    "model": "f87",
    "year": "2015"
  }, {
    "name": "2 Series Grand Tourer F46 (2014-2018)",
    "model": "f46",
    "year": "2015"
  }, {
    "name": "3 Series Coupe E92 (2005-2013)",
    "model": "e92",
    "year": "2010"
  }, {
    "name": "3 Series Convertible E93(2005 - 2013)",
    "model": "e93",
    "year": "2010"
  }, {
    "name": "3 Series Gran Turismo F34(2012 - 2018)",
    "model": "f34",
    "year": "2015"
  }, {
    "name": "3 Series Sedan E90(2004 - 2012)",
    "model": "e90",
    "year": "2010"
  }, {
    "name": "3 Series Sedan F30(2011 - 2018)",
    "model": "f30",
    "year": "2015"
  }, {
    "name": "3 Series Sedan F35(2011 - 2018)",
    "model": "f35",
    "year": "2015"
  }, {
    "name": "3 Series Sedan M3 F80(2012 - 2018)",
    "model": "f80",
    "year": "2015"
  }, {
    "name": "3 Series Touring E91(2004 - 2012)",
    "model": "e91",
    "year": "2010"
  }, {
    "name": "3 Series Touring F31(2011 - 2018)",
    "model": "f31",
    "year": "2015"
  }, {
    "name": "4 Series Convertible F33(2013 - 2017)",
    "model": "f33",
    "year": "2015"
  }, {
    "name": "4 Series Coupé F32 (2012-2018)",
    "model": "f32",
    "year": "2015"
  }, {
    "name": "4 Series Gran Coupé F36 (2013-2018)",
    "model": "f36",
    "year": "2015"
  }, {
    "name": "4 Series M4 Convertible F83 (2013-2018)",
    "model": "f83",
    "year": "2015"
  }, {
    "name": "4 Series M4 Coupé F82 (2013-2018)",
    "model": "f82",
    "year": "2015"
  }, {
    "name": "5 Series Gran Turismo F07 (2008-2017)",
    "model": "f07",
    "year": "2015"
  }, {
    "name": "5 Series Sedan E60 (2001-2010)",
    "model": "e60",
    "year": "2010"
  }, {
    "name": "5 Series Sedan F10 (2009-2016)",
    "model": "f10",
    "year": "2015"
  }, {
    "name": "5 Series Sedan G30 (2015-2018)",
    "model": "g30",
    "year": "2015"
  }, {
    "name": "5 Series Sedan M5 F90 (2016-2018)",
    "model": "m5",
    "year": "2016"
  }, {
    "name": "5 Series Touring G31 (2016-2018)",
    "model": "g31",
    "year": "2016"
  }, {
    "name": "5 Series Wagon E61 (2002-2010)",
    "model": "e61",
    "year": "2010"
  }, {
    "name": "5 Series Wagon F11 (2009-2017)",
    "model": "f11",
    "year": "2015"
  }, {
    "name": "6 Series Convertible E64 (2002-2010)",
    "model": "e64",
    "year": "2010"
  }, {
    "name": "6 Series Convertible F12 (2009-2018)",
    "model": "f12",
    "year": "2015"
  }, {
    "name": "6 Series Coupé E63 (2002-2010)",
    "model": "e63",
    "year": "2010"
  }, {
    "name": "6 Series Coupé F13 (2010-2015)",
    "model": "f13",
    "year": "2015"
  }, {
    "name": "6 Series Gran Coupé F06 (2011-2018)",
    "model": "f06",
    "year": "2015"
  }, {
    "name": "6 Series Gran Turismo G32 (2016-2018)",
    "model": "g32",
    "year": "2016"
  }, {
    "name": "7 Series Hybrid F04 (2008-2012)",
    "model": "f04",
    "year": "2010"
  }, {
    "name": "7 Series Long Sedan E66 (2000-2008)",
    "model": "e66",
    "year": "2008"
  }, {
    "name": "7 Series Long Sedan E67 (2002-2008)",
    "model": "e67",
    "year": "2008"
  }, {
    "name": "7 Series Long Sedan F02 (2007-2015)",
    "model": "f02",
    "year": "2015"
  }, {
    "name": "7 Series Long Sedan F03 (2008-2012)",
    "model": "f03",
    "year": "2010"
  }, {
    "name": "7 Series Long Sedan G12 (2014-2018)",
    "model": "g12",
    "year": "2015"
  }, {
    "name": "7 Series Sedan E65 (2000-2008)",
    "model": "e65",
    "year": "2008"
  }, {
    "name": "7 Series Sedan F01 (2007-2015)",
    "model": "f01",
    "year": "2015"
  }, {
    "name": "7 Series Sedan G11 (2014-2018)",
    "model": "g11",
    "year": "2015"
  }, {
    "name": "I3 (I01) (2013-2018)",
    "model": "i01",
    "year": "2015"
  }, {
    "name": "X1 Series Crossover E84 (2008-2015)",
    "model": "e84",
    "year": "2015"
  }, {
    "name": "X1 Series Crossover F48 (2014-2018)",
    "model": "f48",
    "year": "2015"
  }, {
    "name": "X1 Series Crossover F49 (2014-2018)",
    "model": "f49",
    "year": "2015"
  }, {
    "name": "X2 Series SUV F39 (2016-2018)",
    "model": "f39",
    "year": "2016"
  }, {
    "name": "X3 Series SUV E83 (2003-2010)",
    "model": "e83",
    "year": "2010"
  }, {
    "name": "X3 Series SUV F25 (2009-2017)",
    "model": "f25",
    "year": "2015"
  }, {
    "name": "X3 Series SUV G01 (2016-2018)",
    "model": "g01",
    "year": "2016"
  }, {
    "name": "X4 Series Crossover F26 (2013-2018)",
    "model": "f26",
    "year": "2015"
  }, {
    "name": "X5 Series SUV E70 (2006-2013)",
    "model": "e70",
    "year": "2010"
  }, {
    "name": "X5 Series SUV F15 (2012-2018)",
    "model": "f15",
    "year": "2015"
  }, {
    "name": "X5 Series SUV F85 (2013-2018)",
    "model": "f85",
    "year": "2015"
  }, {
    "name": "X6 Series Crossover E71 (2007-2014)",
    "model": "e71",
    "year": "2010"
  }, {
    "name": "X6 Series Crossover F16 (2013-2018)",
    "model": "f16",
    "year": "2015"
  }, {
    "name": "X6 Series Crossover M F86 (2013-2018)",
    "model": "f86",
    "year": "2015"
  }, {
    "name": "X6 Series Hybrid E72 (2008-2011)",
    "model": "e72",
    "year": "2010"
  }, {
    "name": "Z4 Series Roadster E89 (2008-2016)",
    "model": "e89",
    "year": "2015"
  }, {
    "name": "Mini Clubman F54",
    "model": "f54",
    "year": "2015"
  }, {
    "name": "Mini F55",
    "model": "f55",
    "year": "2015"
  }, {
    "name": "Mini F56",
    "model": "f56",
    "year": "2015"
  }, {
    "name": "Mini Convertible F57",
    "model": "f57",
    "year": "2018"
  }, {
    "name": "Mini Countryman F60",
    "model": "f60",
    "year": "2017"
  }, {
    "name": "Mini Paceman R60",
    "model": "r60",
    "year": "2017"
  }, {
    "name": "Mini Paceman R61",
    "model": "r61",
    "year": "2017"
  }]);