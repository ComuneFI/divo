(window.webpackJsonp=window.webpackJsonp||[]).push([["adminpanel"],{"+2oP":function(e,t,o){"use strict";var n=o("I+eb"),a=o("hh1v"),r=o("6LWA"),i=o("I8vh"),l=o("UMSQ"),s=o("/GqU"),c=o("hBjN"),u=o("tiKp"),p=o("Hd5f"),d=o("rkAj"),f=p("slice"),m=d("slice",{ACCESSORS:!0,0:0,1:2}),b=u("species"),h=[].slice,v=Math.max;n({target:"Array",proto:!0,forced:!f||!m},{slice:function(e,t){var o,n,u,p=s(this),d=l(p.length),f=i(e,d),m=i(void 0===t?d:t,d);if(r(p)&&("function"!=typeof(o=p.constructor)||o!==Array&&!r(o.prototype)?a(o)&&null===(o=o[b])&&(o=void 0):o=void 0,o===Array||void 0===o))return h.call(p,f,m);for(n=new(void 0===o?Array:o)(v(m-f,0)),u=0;f<m;f++,u++)f in p&&c(n,u,p[f]);return n.length=u,n}})},"0X6D":function(e,t,o){"use strict";(function(e){o("eoL8");function n(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}var a=function(){function t(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t)}var o,a,r;return o=t,r=[{key:"show",value:function(){var e=document.createElement("div");e.setAttribute("class","loader loader-default is-active"),e.setAttribute("id","bispinnerloader"),document.body.appendChild(e)}},{key:"hide",value:function(){e("#bispinnerloader").remove()}}],(a=null)&&n(o.prototype,a),r&&n(o,r),t}();t.a=a}).call(this,o("EVdn"))},"15k/":function(e,t,o){"use strict";(function(e){o("SYor");var t=o("JdwY"),n=o.n(t),a=o("ZXQi");e(document).ready((function(){e("#adminpanelcc").click((function(){a.a.eseguicomando("Vuoi pulire tutte le cache?",Routing.generate("fi_pannello_amministrazione_clearcache"))})),e("#adminpanelvcs").click((function(){var e="Vuoi prendere l'ultima versione dei sorgenti dal server "+this.dataset.vcs+"?";a.a.eseguicomando(e,Routing.generate("fi_pannello_amministrazione_getVcs"))})),e("#adminpanelphpunittest").click((function(){a.a.eseguicomando("Vuoi eseguire tutti i test unitari?",Routing.generate("fi_pannello_amministrazione_phpunittest"))})),e("#adminpanelunixcommand").click((function(){var t=e("#unixcommand").val();if(t.trim().length<=0)return n.a.alert({size:"medium",closeButton:!1,title:'<div class="alert alert-warning" role="alert">Attenzione</div>',message:"Specificare un comando valido"}),!1;var o="Vuoi eseguire il comando unix: "+t;a.a.eseguicomando(o,Routing.generate("fi_pannello_amministrazione_unixcommand"),{unixcommand:t})})),e("#adminpanelgenerateentity").click((function(){var t=e("#entityfile").val();if(!t)return n.a.alert({size:"medium",closeButton:!1,title:'<div class="alert alert-warning" role="alert">Attenzione</div>',message:"Specificare un modello mysqlworkbench"}),!1;var o="Vuoi creare i fle di configurazione per le entità partendo dal file: "+t;a.a.eseguicomando(o,Routing.generate("fi_pannello_amministrazione_generateentity"),{file:t})})),e("#adminpanelgenerateformcrud").click((function(){var t=e("#entityform").val();if(!t)return n.a.alert({size:"medium",closeButton:!1,title:'<div class="alert alert-warning" role="alert">Attenzione</div>',message:"Specificare una entity"}),!1;var o=e("#generatemplate").prop("checked"),r="Vuoi creare il crud per il form "+t;a.a.eseguicomando(r,Routing.generate("fi_pannello_amministrazione_generateformcrud"),{entityform:t,generatemplate:o})})),e("#adminpanelaggiornadatabase").click((function(){a.a.eseguicomando("Vuoi aggiornare il database partendo dalla definizione dalle entità esistenti",Routing.generate("fi_pannello_amministrazione_aggiornaschemadatabase"))})),e("#adminpanelsymfonycommand").click((function(){var t=e("#symfonycommand").val();if(t.trim().length<=0)return n.a.alert({size:"medium",closeButton:!1,title:'<div class="alert alert-warning" role="alert">Attenzione</div>',message:"Specificare un comando valido"}),!1;var o="Vuoi eseguire il comando "+t;a.a.eseguicomando(o,Routing.generate("fi_pannello_amministrazione_symfonycommand"),{symfonycommand:t})}))}))}).call(this,o("EVdn"))},"3Jit":function(e,t,o){"use strict";(function(e){o("eoL8");function n(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}var a=function(){function t(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t)}var o,a,r;return o=t,r=[{key:"showErrori",value:function(t){return e("<div>",{id:"corebundlemodalerror"}).css("height","300px").css("overflow-y","scroll").css("overflow-x","hidden").html(t)}},{key:"showMessaggi",value:function(t){return e("<div>",{id:"corebundlemodalinfo"}).css("height","300px").css("overflow-y","scroll").css("overflow-x","hidden").html(t)}}],(a=null)&&n(o.prototype,a),r&&n(o,r),t}();t.a=a}).call(this,o("EVdn"))},Amql:function(e,t,o){},Hd5f:function(e,t,o){var n=o("0Dky"),a=o("tiKp"),r=o("LQDL"),i=a("species");e.exports=function(e){return r>=51||!n((function(){var t=[];return(t.constructor={})[i]=function(){return{foo:1}},1!==t[e](Boolean).foo}))}},JdwY:function(e,t,o){var n,a,r;
/*! @preserve
 * bootbox.js
 * version: 5.4.0
 * author: Nick Payne <nick@kurai.co.uk>
 * license: MIT
 * http://bootboxjs.com/
 */!function(i,l){"use strict";a=[o("EVdn")],void 0===(r="function"==typeof(n=function e(t,o){Object.keys||(Object.keys=(n=Object.prototype.hasOwnProperty,a=!{toString:null}.propertyIsEnumerable("toString"),i=(r=["toString","toLocaleString","valueOf","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","constructor"]).length,function(e){if("function"!=typeof e&&("object"!=typeof e||null===e))throw new TypeError("Object.keys called on non-object");var t,o,l=[];for(t in e)n.call(e,t)&&l.push(t);if(a)for(o=0;o<i;o++)n.call(e,r[o])&&l.push(r[o]);return l}));var n,a,r,i;var l={};l.VERSION="5.0.0";var s={ar:{OK:"موافق",CANCEL:"الغاء",CONFIRM:"تأكيد"},bg_BG:{OK:"Ок",CANCEL:"Отказ",CONFIRM:"Потвърждавам"},br:{OK:"OK",CANCEL:"Cancelar",CONFIRM:"Sim"},cs:{OK:"OK",CANCEL:"Zrušit",CONFIRM:"Potvrdit"},da:{OK:"OK",CANCEL:"Annuller",CONFIRM:"Accepter"},de:{OK:"OK",CANCEL:"Abbrechen",CONFIRM:"Akzeptieren"},el:{OK:"Εντάξει",CANCEL:"Ακύρωση",CONFIRM:"Επιβεβαίωση"},en:{OK:"OK",CANCEL:"Cancel",CONFIRM:"OK"},es:{OK:"OK",CANCEL:"Cancelar",CONFIRM:"Aceptar"},eu:{OK:"OK",CANCEL:"Ezeztatu",CONFIRM:"Onartu"},et:{OK:"OK",CANCEL:"Katkesta",CONFIRM:"OK"},fa:{OK:"قبول",CANCEL:"لغو",CONFIRM:"تایید"},fi:{OK:"OK",CANCEL:"Peruuta",CONFIRM:"OK"},fr:{OK:"OK",CANCEL:"Annuler",CONFIRM:"Confirmer"},he:{OK:"אישור",CANCEL:"ביטול",CONFIRM:"אישור"},hu:{OK:"OK",CANCEL:"Mégsem",CONFIRM:"Megerősít"},hr:{OK:"OK",CANCEL:"Odustani",CONFIRM:"Potvrdi"},id:{OK:"OK",CANCEL:"Batal",CONFIRM:"OK"},it:{OK:"OK",CANCEL:"Annulla",CONFIRM:"Conferma"},ja:{OK:"OK",CANCEL:"キャンセル",CONFIRM:"確認"},ka:{OK:"OK",CANCEL:"გაუქმება",CONFIRM:"დადასტურება"},ko:{OK:"OK",CANCEL:"취소",CONFIRM:"확인"},lt:{OK:"Gerai",CANCEL:"Atšaukti",CONFIRM:"Patvirtinti"},lv:{OK:"Labi",CANCEL:"Atcelt",CONFIRM:"Apstiprināt"},nl:{OK:"OK",CANCEL:"Annuleren",CONFIRM:"Accepteren"},no:{OK:"OK",CANCEL:"Avbryt",CONFIRM:"OK"},pl:{OK:"OK",CANCEL:"Anuluj",CONFIRM:"Potwierdź"},pt:{OK:"OK",CANCEL:"Cancelar",CONFIRM:"Confirmar"},ru:{OK:"OK",CANCEL:"Отмена",CONFIRM:"Применить"},sk:{OK:"OK",CANCEL:"Zrušiť",CONFIRM:"Potvrdiť"},sl:{OK:"OK",CANCEL:"Prekliči",CONFIRM:"Potrdi"},sq:{OK:"OK",CANCEL:"Anulo",CONFIRM:"Prano"},sv:{OK:"OK",CANCEL:"Avbryt",CONFIRM:"OK"},sw:{OK:"Sawa",CANCEL:"Ghairi",CONFIRM:"Thibitisha"},ta:{OK:"சரி",CANCEL:"ரத்து செய்",CONFIRM:"உறுதி செய்"},th:{OK:"ตกลง",CANCEL:"ยกเลิก",CONFIRM:"ยืนยัน"},tr:{OK:"Tamam",CANCEL:"İptal",CONFIRM:"Onayla"},uk:{OK:"OK",CANCEL:"Відміна",CONFIRM:"Прийняти"},zh_CN:{OK:"OK",CANCEL:"取消",CONFIRM:"确认"},zh_TW:{OK:"OK",CANCEL:"取消",CONFIRM:"確認"}},c={dialog:'<div class="bootbox modal" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><div class="bootbox-body"></div></div></div></div></div>',header:'<div class="modal-header"><h5 class="modal-title"></h5></div>',footer:'<div class="modal-footer"></div>',closeButton:'<button type="button" class="bootbox-close-button close" aria-hidden="true">&times;</button>',form:'<form class="bootbox-form"></form>',button:'<button type="button" class="btn"></button>',option:"<option></option>",promptMessage:'<div class="bootbox-prompt-message"></div>',inputs:{text:'<input class="bootbox-input bootbox-input-text form-control" autocomplete="off" type="text" />',textarea:'<textarea class="bootbox-input bootbox-input-textarea form-control"></textarea>',email:'<input class="bootbox-input bootbox-input-email form-control" autocomplete="off" type="email" />',select:'<select class="bootbox-input bootbox-input-select form-control"></select>',checkbox:'<div class="form-check checkbox"><label class="form-check-label"><input class="form-check-input bootbox-input bootbox-input-checkbox" type="checkbox" /></label></div>',radio:'<div class="form-check radio"><label class="form-check-label"><input class="form-check-input bootbox-input bootbox-input-radio" type="radio" name="bootbox-radio" /></label></div>',date:'<input class="bootbox-input bootbox-input-date form-control" autocomplete="off" type="date" />',time:'<input class="bootbox-input bootbox-input-time form-control" autocomplete="off" type="time" />',number:'<input class="bootbox-input bootbox-input-number form-control" autocomplete="off" type="number" />',password:'<input class="bootbox-input bootbox-input-password form-control" autocomplete="off" type="password" />',range:'<input class="bootbox-input bootbox-input-range form-control-range" autocomplete="off" type="range" />'}},u={locale:"en",backdrop:"static",animate:!0,className:null,closeButton:!0,show:!0,container:"body",value:"",inputType:"text",swapButtonOrder:!1,centerVertical:!1,multiple:!1,scrollable:!1};function p(e,o,n){return t.extend(!0,{},e,function(e,t){var o=e.length,n={};if(o<1||o>2)throw new Error("Invalid argument length");return 2===o||"string"==typeof e[0]?(n[t[0]]=e[0],n[t[1]]=e[1]):n=e[0],n}(o,n))}function d(e,t,n,a){var r;a&&a[0]&&(r=a[0].locale||u.locale,(a[0].swapButtonOrder||u.swapButtonOrder)&&(t=t.reverse()));var i,l,s,c={className:"bootbox-"+e,buttons:f(t,r)};return i=p(c,a,n),s={},h(l=t,(function(e,t){s[t]=!0})),h(i.buttons,(function(e){if(s[e]===o)throw new Error('button key "'+e+'" is not allowed (options are '+l.join(" ")+")")})),i}function f(e,t){for(var o={},n=0,a=e.length;n<a;n++){var r=e[n],i=r.toLowerCase(),l=r.toUpperCase();o[i]={label:m(l,t)}}return o}function m(e,t){var o=s[t];return o?o[e]:s.en[e]}function b(e){return Object.keys(e).length}function h(e,o){var n=0;t.each(e,(function(e,t){o(e,t,n++)}))}function v(e){e.data.dialog.find(".bootbox-accept").first().trigger("focus")}function g(e){e.target===e.data.dialog[0]&&e.data.dialog.remove()}function w(e){e.target===e.data.dialog[0]&&(e.data.dialog.off("escape.close.bb"),e.data.dialog.off("click"))}function C(e,o,n){e.stopPropagation(),e.preventDefault(),t.isFunction(n)&&!1===n.call(o,e)||o.modal("hide")}function y(e,t,n){var a=!1,r=!0,i=!0;if("date"===e)t===o||(r=x(t))?n===o||(i=x(n))||console.warn('Browsers which natively support the "date" input type expect date values to be of the form "YYYY-MM-DD" (see ISO-8601 https://www.iso.org/iso-8601-date-and-time-format.html). Bootbox does not enforce this rule, but your max value may not be enforced by this browser.'):console.warn('Browsers which natively support the "date" input type expect date values to be of the form "YYYY-MM-DD" (see ISO-8601 https://www.iso.org/iso-8601-date-and-time-format.html). Bootbox does not enforce this rule, but your min value may not be enforced by this browser.');else if("time"===e){if(t!==o&&!(r=O(t)))throw new Error('"min" is not a valid time. See https://www.w3.org/TR/2012/WD-html-markup-20120315/datatypes.html#form.data.time for more information.');if(n!==o&&!(i=O(n)))throw new Error('"max" is not a valid time. See https://www.w3.org/TR/2012/WD-html-markup-20120315/datatypes.html#form.data.time for more information.')}else{if(t!==o&&isNaN(t))throw r=!1,new Error('"min" must be a valid number. See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-min for more information.');if(n!==o&&isNaN(n))throw i=!1,new Error('"max" must be a valid number. See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-max for more information.')}if(r&&i){if(n<=t)throw new Error('"max" must be greater than "min". See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-max for more information.');a=!0}return a}function O(e){return/([01][0-9]|2[0-3]):[0-5][0-9]?:[0-5][0-9]/.test(e)}function x(e){return/(\d{4})-(\d{2})-(\d{2})/.test(e)}return l.locales=function(e){return e?s[e]:s},l.addLocale=function(e,o){return t.each(["OK","CANCEL","CONFIRM"],(function(e,t){if(!o[t])throw new Error('Please supply a translation for "'+t+'"')})),s[e]={OK:o.OK,CANCEL:o.CANCEL,CONFIRM:o.CONFIRM},l},l.removeLocale=function(e){if("en"===e)throw new Error('"en" is used as the default and fallback locale and cannot be removed.');return delete s[e],l},l.setLocale=function(e){return l.setDefaults("locale",e)},l.setDefaults=function(){var e={};return 2===arguments.length?e[arguments[0]]=arguments[1]:e=arguments[0],t.extend(u,e),l},l.hideAll=function(){return t(".bootbox").modal("hide"),l},l.init=function(o){return e(o||t)},l.dialog=function(e){if(t.fn.modal===o)throw new Error('"$.fn.modal" is not defined; please double check you have included the Bootstrap JavaScript library. See http://getbootstrap.com/javascript/ for more details.');if(e=function(e){var o,n;if("object"!=typeof e)throw new Error("Please supply an object of options");if(!e.message)throw new Error('"message" option must not be null or an empty string.');(e=t.extend({},u,e)).buttons||(e.buttons={});return o=e.buttons,n=b(o),h(o,(function(a,r,i){if(t.isFunction(r)&&(r=o[a]={callback:r}),"object"!==t.type(r))throw new Error('button with key "'+a+'" must be an object');if(r.label||(r.label=a),!r.className){var l=!1;l=e.swapButtonOrder?0===i:i===n-1,r.className=n<=2&&l?"btn-primary":"btn-secondary btn-default"}})),e}(e),t.fn.modal.Constructor.VERSION){e.fullBootstrapVersion=t.fn.modal.Constructor.VERSION;var n=e.fullBootstrapVersion.indexOf(".");e.bootstrap=e.fullBootstrapVersion.substring(0,n)}else e.bootstrap="2",e.fullBootstrapVersion="2.3.2",console.warn("Bootbox will *mostly* work with Bootstrap 2, but we do not officially support it. Please upgrade, if possible.");var a=t(c.dialog),r=a.find(".modal-dialog"),i=a.find(".modal-body"),l=t(c.header),s=t(c.footer),p=e.buttons,d={onEscape:e.onEscape};if(i.find(".bootbox-body").html(e.message),b(e.buttons)>0&&(h(p,(function(e,o){var n=t(c.button);switch(n.data("bb-handler",e),n.addClass(o.className),e){case"ok":case"confirm":n.addClass("bootbox-accept");break;case"cancel":n.addClass("bootbox-cancel")}n.html(o.label),s.append(n),d[e]=o.callback})),i.after(s)),!0===e.animate&&a.addClass("fade"),e.className&&a.addClass(e.className),e.size)switch(e.fullBootstrapVersion.substring(0,3)<"3.1"&&console.warn('"size" requires Bootstrap 3.1.0 or higher. You appear to be using '+e.fullBootstrapVersion+". Please upgrade to use this option."),e.size){case"small":case"sm":r.addClass("modal-sm");break;case"large":case"lg":r.addClass("modal-lg");break;case"extra-large":case"xl":r.addClass("modal-xl"),e.fullBootstrapVersion.substring(0,3)<"4.2"&&console.warn('Using size "xl"/"extra-large" requires Bootstrap 4.2.0 or higher. You appear to be using '+e.fullBootstrapVersion+". Please upgrade to use this option.")}if(e.scrollable&&(r.addClass("modal-dialog-scrollable"),e.fullBootstrapVersion.substring(0,3)<"4.3"&&console.warn('Using "scrollable" requires Bootstrap 4.3.0 or higher. You appear to be using '+e.fullBootstrapVersion+". Please upgrade to use this option.")),e.title&&(i.before(l),a.find(".modal-title").html(e.title)),e.closeButton){var f=t(c.closeButton);e.title?e.bootstrap>3?a.find(".modal-header").append(f):a.find(".modal-header").prepend(f):f.prependTo(i)}if(e.centerVertical&&(r.addClass("modal-dialog-centered"),e.fullBootstrapVersion<"4.0.0"&&console.warn('"centerVertical" requires Bootstrap 4.0.0-beta.3 or higher. You appear to be using '+e.fullBootstrapVersion+". Please upgrade to use this option.")),a.one("hide.bs.modal",{dialog:a},w),e.onHide){if(!t.isFunction(e.onHide))throw new Error('Argument supplied to "onHide" must be a function');a.on("hide.bs.modal",e.onHide)}if(a.one("hidden.bs.modal",{dialog:a},g),e.onHidden){if(!t.isFunction(e.onHidden))throw new Error('Argument supplied to "onHidden" must be a function');a.on("hidden.bs.modal",e.onHidden)}if(e.onShow){if(!t.isFunction(e.onShow))throw new Error('Argument supplied to "onShow" must be a function');a.on("show.bs.modal",e.onShow)}if(a.one("shown.bs.modal",{dialog:a},v),e.onShown){if(!t.isFunction(e.onShown))throw new Error('Argument supplied to "onShown" must be a function');a.on("shown.bs.modal",e.onShown)}return"static"!==e.backdrop&&a.on("click.dismiss.bs.modal",(function(e){a.children(".modal-backdrop").length&&(e.currentTarget=a.children(".modal-backdrop").get(0)),e.target===e.currentTarget&&a.trigger("escape.close.bb")})),a.on("escape.close.bb",(function(e){d.onEscape&&C(e,a,d.onEscape)})),a.on("click",".modal-footer button:not(.disabled)",(function(e){var n=t(this).data("bb-handler");n!==o&&C(e,a,d[n])})),a.on("click",".bootbox-close-button",(function(e){C(e,a,d.onEscape)})),a.on("keyup",(function(e){27===e.which&&a.trigger("escape.close.bb")})),t(e.container).append(a),a.modal({backdrop:!!e.backdrop&&"static",keyboard:!1,show:!1}),e.show&&a.modal("show"),a},l.alert=function(){var e;if((e=d("alert",["ok"],["message","callback"],arguments)).callback&&!t.isFunction(e.callback))throw new Error('alert requires the "callback" property to be a function when provided');return e.buttons.ok.callback=e.onEscape=function(){return!t.isFunction(e.callback)||e.callback.call(this)},l.dialog(e)},l.confirm=function(){var e;if(e=d("confirm",["cancel","confirm"],["message","callback"],arguments),!t.isFunction(e.callback))throw new Error("confirm requires a callback");return e.buttons.cancel.callback=e.onEscape=function(){return e.callback.call(this,!1)},e.buttons.confirm.callback=function(){return e.callback.call(this,!0)},l.dialog(e)},l.prompt=function(){var e,n,a,r,i,s;if(a=t(c.form),(e=d("prompt",["cancel","confirm"],["title","callback"],arguments)).value||(e.value=u.value),e.inputType||(e.inputType=u.inputType),i=e.show===o?u.show:e.show,e.show=!1,e.buttons.cancel.callback=e.onEscape=function(){return e.callback.call(this,null)},e.buttons.confirm.callback=function(){var o;if("checkbox"===e.inputType)o=r.find("input:checked").map((function(){return t(this).val()})).get();else if("radio"===e.inputType)o=r.find("input:checked").val();else{if(r[0].checkValidity&&!r[0].checkValidity())return!1;o="select"===e.inputType&&!0===e.multiple?r.find("option:selected").map((function(){return t(this).val()})).get():r.val()}return e.callback.call(this,o)},!e.title)throw new Error("prompt requires a title");if(!t.isFunction(e.callback))throw new Error("prompt requires a callback");if(!c.inputs[e.inputType])throw new Error("Invalid prompt type");switch(r=t(c.inputs[e.inputType]),e.inputType){case"text":case"textarea":case"email":case"password":r.val(e.value),e.placeholder&&r.attr("placeholder",e.placeholder),e.pattern&&r.attr("pattern",e.pattern),e.maxlength&&r.attr("maxlength",e.maxlength),e.required&&r.prop({required:!0}),e.rows&&!isNaN(parseInt(e.rows))&&"textarea"===e.inputType&&r.attr({rows:e.rows});break;case"date":case"time":case"number":case"range":if(r.val(e.value),e.placeholder&&r.attr("placeholder",e.placeholder),e.pattern&&r.attr("pattern",e.pattern),e.required&&r.prop({required:!0}),"date"!==e.inputType&&e.step){if(!("any"===e.step||!isNaN(e.step)&&parseFloat(e.step)>0))throw new Error('"step" must be a valid positive number or the value "any". See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-step for more information.');r.attr("step",e.step)}y(e.inputType,e.min,e.max)&&(e.min!==o&&r.attr("min",e.min),e.max!==o&&r.attr("max",e.max));break;case"select":var p={};if(s=e.inputOptions||[],!t.isArray(s))throw new Error("Please pass an array of input options");if(!s.length)throw new Error('prompt with "inputType" set to "select" requires at least one option');e.placeholder&&r.attr("placeholder",e.placeholder),e.required&&r.prop({required:!0}),e.multiple&&r.prop({multiple:!0}),h(s,(function(e,n){var a=r;if(n.value===o||n.text===o)throw new Error('each option needs a "value" property and a "text" property');n.group&&(p[n.group]||(p[n.group]=t("<optgroup />").attr("label",n.group)),a=p[n.group]);var i=t(c.option);i.attr("value",n.value).text(n.text),a.append(i)})),h(p,(function(e,t){r.append(t)})),r.val(e.value);break;case"checkbox":var f=t.isArray(e.value)?e.value:[e.value];if(!(s=e.inputOptions||[]).length)throw new Error('prompt with "inputType" set to "checkbox" requires at least one option');r=t('<div class="bootbox-checkbox-list"></div>'),h(s,(function(n,a){if(a.value===o||a.text===o)throw new Error('each option needs a "value" property and a "text" property');var i=t(c.inputs[e.inputType]);i.find("input").attr("value",a.value),i.find("label").append("\n"+a.text),h(f,(function(e,t){t===a.value&&i.find("input").prop("checked",!0)})),r.append(i)}));break;case"radio":if(e.value!==o&&t.isArray(e.value))throw new Error('prompt with "inputType" set to "radio" requires a single, non-array value for "value"');if(!(s=e.inputOptions||[]).length)throw new Error('prompt with "inputType" set to "radio" requires at least one option');r=t('<div class="bootbox-radiobutton-list"></div>');var m=!0;h(s,(function(n,a){if(a.value===o||a.text===o)throw new Error('each option needs a "value" property and a "text" property');var i=t(c.inputs[e.inputType]);i.find("input").attr("value",a.value),i.find("label").append("\n"+a.text),e.value!==o&&a.value===e.value&&(i.find("input").prop("checked",!0),m=!1),r.append(i)})),m&&r.find('input[type="radio"]').first().prop("checked",!0)}if(a.append(r),a.on("submit",(function(e){e.preventDefault(),e.stopPropagation(),n.find(".bootbox-accept").trigger("click")})),""!==t.trim(e.message)){var b=t(c.promptMessage).html(e.message);a.prepend(b),e.message=a}else e.message=a;return(n=l.dialog(e)).off("shown.bs.modal",v),n.on("shown.bs.modal",(function(){r.focus()})),!0===i&&n.modal("show"),n},l})?n.apply(t,a):n)||(e.exports=r)}()},LQDL:function(e,t,o){var n,a,r=o("2oRo"),i=o("NC/Y"),l=r.process,s=l&&l.versions,c=s&&s.v8;c?a=(n=c.split("."))[0]+n[1]:i&&(!(n=i.match(/Edge\/(\d+)/))||n[1]>=74)&&(n=i.match(/Chrome\/(\d+)/))&&(a=n[1]),e.exports=a&&+a},"NC/Y":function(e,t,o){var n=o("0GbY");e.exports=n("navigator","userAgent")||""},SYor:function(e,t,o){"use strict";var n=o("I+eb"),a=o("WKiH").trim;n({target:"String",proto:!0,forced:o("yNLB")("trim")},{trim:function(){return a(this)}})},WJkJ:function(e,t){e.exports="\t\n\v\f\r                　\u2028\u2029\ufeff"},WKiH:function(e,t,o){var n=o("HYAF"),a="["+o("WJkJ")+"]",r=RegExp("^"+a+a+"*"),i=RegExp(a+a+"*$"),l=function(e){return function(t){var o=String(n(t));return 1&e&&(o=o.replace(r,"")),2&e&&(o=o.replace(i,"")),o}};e.exports={start:l(1),end:l(2),trim:l(3)}},ZXQi:function(e,t,o){"use strict";(function(e){o("eoL8");var n=o("JdwY"),a=o.n(n),r=o("0X6D"),i=o("3Jit");function l(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}var s=function(){function t(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t)}var o,n,s;return o=t,s=[{key:"eseguicomando",value:function(t,o,n){n=n||{},a.a.confirm({message:t,buttons:{cancel:{className:"btn btn-default biconfirmno",label:'<i class="fa fa-times"></i> Annulla'},confirm:{className:"btn btn-primary biconfirmyes",label:'<i class="fa fa-check"></i> Si'}},callback:function(t){t&&(r.a.show(),e.ajax({url:o,data:n}).done((function(t){var o=e("<div>").attr("role","alert").attr("class","alert alert-success alert-dismissible fade show");o.html("<strong>Operazione conclusa</strong>"),a.a.alert({size:"large",message:e.merge(o,i.a.showMessaggi(t)),buttons:{ok:{className:"btn btn-primary biconfirmok",label:'<i class="fa fa-check"></i> Ok'}}}),r.a.hide()})).fail((function(t,o){var n=e("<div>").attr("role","alert").attr("class","alert alert-warning alert-dismissible fade show");n.html("<strong>Si è verificato un errore</strong>"),a.a.alert({size:"large",closeButton:!1,message:e.merge(n,i.a.showErrori(t.responseText))}),r.a.hide()})))}})}}],(n=null)&&l(o.prototype,n),s&&l(o,s),t}();t.a=s}).call(this,o("EVdn"))},dQ5b:function(e,t,o){"use strict";o.r(t);o("Amql"),o("m4TX"),o("15k/"),o("ZXQi")},eoL8:function(e,t,o){var n=o("I+eb"),a=o("g6v/");n({target:"Object",stat:!0,forced:!a,sham:!a},{defineProperty:o("m/L8").f})},hBjN:function(e,t,o){"use strict";var n=o("wE6v"),a=o("m/L8"),r=o("XGwC");e.exports=function(e,t,o){var i=n(t);i in e?a.f(e,i,r(0,o)):e[i]=o}},m4TX:function(e,t,o){"use strict";(function(e){o("fbCW"),o("yXV3"),o("+2oP"),o("SYor"),e(document).unbind("keyup").keyup((function(t){13==t.which&&window.currentfunction&&(t.preventDefault(),e("#adminpanel"+window.currentfunction).click(),window.currentfunction="")})),e(document).ready((function(){e("#symfonycommand").focusin((function(){window.currentfunction="symfonycommand"})),e("#unixcommand").focusin((function(){window.currentfunction="unixcommand"})),e("#entityform").focusin((function(){window.currentfunction=""})),e("#entityfile").focusin((function(){window.currentfunction=""})),e((function(){e('[data-toggle="tooltip"]').tooltip()}))})),e(document).on("click",".autocomplete-list-text",(function(t){t.preventDefault();var o="";o=e(this).text().indexOf("Label")?e(this).text().slice(0,-5).trim():e(this).text().trim(),e(this).closest("div").find(":input").val(o),e(this).closest("ul").removeClass("autocomplete-list-show"),e(this).closest("div").find(":input").focus()}))}).call(this,o("EVdn"))},pkCn:function(e,t,o){"use strict";var n=o("0Dky");e.exports=function(e,t){var o=[][e];return!!o&&n((function(){o.call(null,t||function(){throw 1},1)}))}},yNLB:function(e,t,o){var n=o("0Dky"),a=o("WJkJ");e.exports=function(e){return n((function(){return!!a[e]()||"​᠎"!="​᠎"[e]()||a[e].name!==e}))}},yXV3:function(e,t,o){"use strict";var n=o("I+eb"),a=o("TWQb").indexOf,r=o("pkCn"),i=o("rkAj"),l=[].indexOf,s=!!l&&1/[1].indexOf(1,-0)<0,c=r("indexOf"),u=i("indexOf",{ACCESSORS:!0,1:0});n({target:"Array",proto:!0,forced:s||!c||!u},{indexOf:function(e){return s?l.apply(this,arguments)||0:a(this,e,arguments.length>1?arguments[1]:void 0)}})}},[["dQ5b","runtime",0,2]]]);