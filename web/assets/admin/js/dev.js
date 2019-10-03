Vue.config.warnHandler = function (msg, vm, trace) {
    // `trace` is the component hierarchy trace
};
Vue.config.productionTip = false;
Vue.config.devtools = true;


let selected_items_refG,
    selected_items_retG,
    perv_selected_items_retG,
    testGlobalObj,
    selectedObj;