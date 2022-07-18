

/*!
 * clipboard.js v2.0.0
 * https://zenorocha.github.io/clipboard.js
 * 
 * Licensed MIT © Zeno Rocha
 */
!function (t, e) { "object" == typeof exports && "object" == typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define([], e) : "object" == typeof exports ? exports.ClipboardJS = e() : t.ClipboardJS = e() }(this, function () { return function (t) { function e(o) { if (n[o]) return n[o].exports; var r = n[o] = { i: o, l: !1, exports: {} }; return t[o].call(r.exports, r, r.exports, e), r.l = !0, r.exports } var n = {}; return e.m = t, e.c = n, e.i = function (t) { return t }, e.d = function (t, n, o) { e.o(t, n) || Object.defineProperty(t, n, { configurable: !1, enumerable: !0, get: o }) }, e.n = function (t) { var n = t && t.__esModule ? function () { return t.default } : function () { return t }; return e.d(n, "a", n), n }, e.o = function (t, e) { return Object.prototype.hasOwnProperty.call(t, e) }, e.p = "", e(e.s = 3) }([function (t, e, n) { var o, r, i; !function (a, c) { r = [t, n(7)], o = c, void 0 !== (i = "function" == typeof o ? o.apply(e, r) : o) && (t.exports = i) }(0, function (t, e) { "use strict"; function n(t, e) { if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function") } var o = function (t) { return t && t.__esModule ? t : { default: t } }(e), r = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (t) { return typeof t } : function (t) { return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t }, i = function () { function t(t, e) { for (var n = 0; n < e.length; n++) { var o = e[n]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(t, o.key, o) } } return function (e, n, o) { return n && t(e.prototype, n), o && t(e, o), e } }(), a = function () { function t(e) { n(this, t), this.resolveOptions(e), this.initSelection() } return i(t, [{ key: "resolveOptions", value: function () { var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {}; this.action = t.action, this.container = t.container, this.emitter = t.emitter, this.target = t.target, this.text = t.text, this.trigger = t.trigger, this.selectedText = "" } }, { key: "initSelection", value: function () { this.text ? this.selectFake() : this.target && this.selectTarget() } }, { key: "selectFake", value: function () { var t = this, e = "rtl" == document.documentElement.getAttribute("dir"); this.removeFake(), this.fakeHandlerCallback = function () { return t.removeFake() }, this.fakeHandler = this.container.addEventListener("click", this.fakeHandlerCallback) || !0, this.fakeElem = document.createElement("textarea"), this.fakeElem.style.fontSize = "12pt", this.fakeElem.style.border = "0", this.fakeElem.style.padding = "0", this.fakeElem.style.margin = "0", this.fakeElem.style.position = "absolute", this.fakeElem.style[e ? "right" : "left"] = "-9999px"; var n = window.pageYOffset || document.documentElement.scrollTop; this.fakeElem.style.top = n + "px", this.fakeElem.setAttribute("readonly", ""), this.fakeElem.value = this.text, this.container.appendChild(this.fakeElem), this.selectedText = (0, o.default)(this.fakeElem), this.copyText() } }, { key: "removeFake", value: function () { this.fakeHandler && (this.container.removeEventListener("click", this.fakeHandlerCallback), this.fakeHandler = null, this.fakeHandlerCallback = null), this.fakeElem && (this.container.removeChild(this.fakeElem), this.fakeElem = null) } }, { key: "selectTarget", value: function () { this.selectedText = (0, o.default)(this.target), this.copyText() } }, { key: "copyText", value: function () { var t = void 0; try { t = document.execCommand(this.action) } catch (e) { t = !1 } this.handleResult(t) } }, { key: "handleResult", value: function (t) { this.emitter.emit(t ? "success" : "error", { action: this.action, text: this.selectedText, trigger: this.trigger, clearSelection: this.clearSelection.bind(this) }) } }, { key: "clearSelection", value: function () { this.trigger && this.trigger.focus(), window.getSelection().removeAllRanges() } }, { key: "destroy", value: function () { this.removeFake() } }, { key: "action", set: function () { var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "copy"; if (this._action = t, "copy" !== this._action && "cut" !== this._action) throw new Error('Invalid "action" value, use either "copy" or "cut"') }, get: function () { return this._action } }, { key: "target", set: function (t) { if (void 0 !== t) { if (!t || "object" !== (void 0 === t ? "undefined" : r(t)) || 1 !== t.nodeType) throw new Error('Invalid "target" value, use a valid Element'); if ("copy" === this.action && t.hasAttribute("disabled")) throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute'); if ("cut" === this.action && (t.hasAttribute("readonly") || t.hasAttribute("disabled"))) throw new Error('Invalid "target" attribute. You can\'t cut text from elements with "readonly" or "disabled" attributes'); this._target = t } }, get: function () { return this._target } }]), t }(); t.exports = a }) }, function (t, e, n) { function o(t, e, n) { if (!t && !e && !n) throw new Error("Missing required arguments"); if (!c.string(e)) throw new TypeError("Second argument must be a String"); if (!c.fn(n)) throw new TypeError("Third argument must be a Function"); if (c.node(t)) return r(t, e, n); if (c.nodeList(t)) return i(t, e, n); if (c.string(t)) return a(t, e, n); throw new TypeError("First argument must be a String, HTMLElement, HTMLCollection, or NodeList") } function r(t, e, n) { return t.addEventListener(e, n), { destroy: function () { t.removeEventListener(e, n) } } } function i(t, e, n) { return Array.prototype.forEach.call(t, function (t) { t.addEventListener(e, n) }), { destroy: function () { Array.prototype.forEach.call(t, function (t) { t.removeEventListener(e, n) }) } } } function a(t, e, n) { return u(document.body, t, e, n) } var c = n(6), u = n(5); t.exports = o }, function (t, e) { function n() { } n.prototype = { on: function (t, e, n) { var o = this.e || (this.e = {}); return (o[t] || (o[t] = [])).push({ fn: e, ctx: n }), this }, once: function (t, e, n) { function o() { r.off(t, o), e.apply(n, arguments) } var r = this; return o._ = e, this.on(t, o, n) }, emit: function (t) { var e = [].slice.call(arguments, 1), n = ((this.e || (this.e = {}))[t] || []).slice(), o = 0, r = n.length; for (o; o < r; o++)n[o].fn.apply(n[o].ctx, e); return this }, off: function (t, e) { var n = this.e || (this.e = {}), o = n[t], r = []; if (o && e) for (var i = 0, a = o.length; i < a; i++)o[i].fn !== e && o[i].fn._ !== e && r.push(o[i]); return r.length ? n[t] = r : delete n[t], this } }, t.exports = n }, function (t, e, n) { var o, r, i; !function (a, c) { r = [t, n(0), n(2), n(1)], o = c, void 0 !== (i = "function" == typeof o ? o.apply(e, r) : o) && (t.exports = i) }(0, function (t, e, n, o) { "use strict"; function r(t) { return t && t.__esModule ? t : { default: t } } function i(t, e) { if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function") } function a(t, e) { if (!t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return !e || "object" != typeof e && "function" != typeof e ? t : e } function c(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function, not " + typeof e); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, enumerable: !1, writable: !0, configurable: !0 } }), e && (Object.setPrototypeOf ? Object.setPrototypeOf(t, e) : t.__proto__ = e) } function u(t, e) { var n = "data-clipboard-" + t; if (e.hasAttribute(n)) return e.getAttribute(n) } var l = r(e), s = r(n), f = r(o), d = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (t) { return typeof t } : function (t) { return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t }, h = function () { function t(t, e) { for (var n = 0; n < e.length; n++) { var o = e[n]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(t, o.key, o) } } return function (e, n, o) { return n && t(e.prototype, n), o && t(e, o), e } }(), p = function (t) { function e(t, n) { i(this, e); var o = a(this, (e.__proto__ || Object.getPrototypeOf(e)).call(this)); return o.resolveOptions(n), o.listenClick(t), o } return c(e, t), h(e, [{ key: "resolveOptions", value: function () { var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {}; this.action = "function" == typeof t.action ? t.action : this.defaultAction, this.target = "function" == typeof t.target ? t.target : this.defaultTarget, this.text = "function" == typeof t.text ? t.text : this.defaultText, this.container = "object" === d(t.container) ? t.container : document.body } }, { key: "listenClick", value: function (t) { var e = this; this.listener = (0, f.default)(t, "click", function (t) { return e.onClick(t) }) } }, { key: "onClick", value: function (t) { var e = t.delegateTarget || t.currentTarget; this.clipboardAction && (this.clipboardAction = null), this.clipboardAction = new l.default({ action: this.action(e), target: this.target(e), text: this.text(e), container: this.container, trigger: e, emitter: this }) } }, { key: "defaultAction", value: function (t) { return u("action", t) } }, { key: "defaultTarget", value: function (t) { var e = u("target", t); if (e) return document.querySelector(e) } }, { key: "defaultText", value: function (t) { return u("text", t) } }, { key: "destroy", value: function () { this.listener.destroy(), this.clipboardAction && (this.clipboardAction.destroy(), this.clipboardAction = null) } }], [{ key: "isSupported", value: function () { var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : ["copy", "cut"], e = "string" == typeof t ? [t] : t, n = !!document.queryCommandSupported; return e.forEach(function (t) { n = n && !!document.queryCommandSupported(t) }), n } }]), e }(s.default); t.exports = p }) }, function (t, e) { function n(t, e) { for (; t && t.nodeType !== o;) { if ("function" == typeof t.matches && t.matches(e)) return t; t = t.parentNode } } var o = 9; if ("undefined" != typeof Element && !Element.prototype.matches) { var r = Element.prototype; r.matches = r.matchesSelector || r.mozMatchesSelector || r.msMatchesSelector || r.oMatchesSelector || r.webkitMatchesSelector } t.exports = n }, function (t, e, n) { function o(t, e, n, o, r) { var a = i.apply(this, arguments); return t.addEventListener(n, a, r), { destroy: function () { t.removeEventListener(n, a, r) } } } function r(t, e, n, r, i) { return "function" == typeof t.addEventListener ? o.apply(null, arguments) : "function" == typeof n ? o.bind(null, document).apply(null, arguments) : ("string" == typeof t && (t = document.querySelectorAll(t)), Array.prototype.map.call(t, function (t) { return o(t, e, n, r, i) })) } function i(t, e, n, o) { return function (n) { n.delegateTarget = a(n.target, e), n.delegateTarget && o.call(t, n) } } var a = n(4); t.exports = r }, function (t, e) { e.node = function (t) { return void 0 !== t && t instanceof HTMLElement && 1 === t.nodeType }, e.nodeList = function (t) { var n = Object.prototype.toString.call(t); return void 0 !== t && ("[object NodeList]" === n || "[object HTMLCollection]" === n) && "length" in t && (0 === t.length || e.node(t[0])) }, e.string = function (t) { return "string" == typeof t || t instanceof String }, e.fn = function (t) { return "[object Function]" === Object.prototype.toString.call(t) } }, function (t, e) { function n(t) { var e; if ("SELECT" === t.nodeName) t.focus(), e = t.value; else if ("INPUT" === t.nodeName || "TEXTAREA" === t.nodeName) { var n = t.hasAttribute("readonly"); n || t.setAttribute("readonly", ""), t.select(), t.setSelectionRange(0, t.value.length), n || t.removeAttribute("readonly"), e = t.value } else { t.hasAttribute("contenteditable") && t.focus(); var o = window.getSelection(), r = document.createRange(); r.selectNodeContents(t), o.removeAllRanges(), o.addRange(r), e = o.toString() } return e } t.exports = n }]) });

jQuery(document).ready(function () {
  "use strict";
  // Background Image
  jQuery(".bg_img").each(function (i, elem) {
    var img = jQuery(elem);
    jQuery(this).hide();
    jQuery(this).parent().css({
      background: "url(" + img.attr("src") + ") no-repeat center center",
    });
  });

  // click to scroll
  $('.collapse-box').on('shown.bs.collapse', function () {
    $(".customscroll").mCustomScrollbar("scrollTo", $(this));
  });

  // tooltip init
  $('[data-toggle="tooltip"]').tooltip()

  // popover init
  $('[data-toggle="popover"]').popover()

  // form-control on focus add class
  $(".form-control").on('focus', function () {
    $(this).parent().addClass("focus");
  })
  $(".form-control").on('focusout', function () {
    $(this).parent().removeClass("focus");
  })

  // Dropdown Slide Animation
  $('.dropdown').on('show.bs.dropdown', function (e) {
    $(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
  });
  $('.dropdown').on('hide.bs.dropdown', function (e) {
    $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
  });

  // sidebar menu icon
  $('.menu-icon').on('click', function () {
    $(this).toggleClass('open');
    $('.left-side-bar').toggleClass('open');
  });

  var w = $(window).width();
  $(document).on('touchstart click', function (e) {
    if ($(e.target).parents('.left-side-bar').length == 0 && !$(e.target).is('.menu-icon, .menu-icon span')) {
      $('.left-side-bar').removeClass('open');
      $('.menu-icon').removeClass('open');
    };
  });
  $(window).on('resize', function () {
    var w = $(window).width();
    if ($(window).width() > 1200) {
      $('.left-side-bar').removeClass('open');
      $('.menu-icon').removeClass('open');
    }
  });


  // sidebar menu Active Class
  $('#accordion-menu').each(function () {
    var vars = window.location.href.split("/").pop();
    var varss = window.location.pathname;

    $(this).find('a[href="' + varss + '"]').addClass('active');
  });


  // click to copy icon
  $(".fa-hover").click(function (event) {
    event.preventDefault();
    var $html = $(this).find('.icon-copy').first();
    var str = $html.prop('outerHTML');
    CopyToClipboard(str, true, "Copied");
  });
  var clipboard = new ClipboardJS('.code-copy');
  clipboard.on('success', function (e) {
    CopyToClipboard('', true, "Copied");
    e.clearSelection();
  });


  // var color = $('.btn').data('color');
  // console.log(color);
  // $('.btn').style('color'+color);
  $("[data-color]").each(function () {
    $(this).css('color', $(this).attr('data-color'));
  });
  $("[data-bgcolor]").each(function () {
    $(this).css('background-color', $(this).attr('data-bgcolor'));
  });
  $("[data-border]").each(function () {
    $(this).css('border', $(this).attr('data-border'));
  });

  $("#accordion-menu").vmenuModule({
    Speed: 400,
    autostart: false,
    autohide: false
  });

});

// sidebar menu accordion
(function ($) {
  $.fn.vmenuModule = function (option) {
    var obj,
      item;
    var options = $.extend({
      Speed: 220,
      autostart: true,
      autohide: 1
    },
      option);
    obj = $(this);

    item = obj.find("ul").parent("li").children("a");
    item.attr("data-option", "off");

    item.unbind('click').on("click", function () {
      var a = $(this);
      if (options.autohide) {
        a.parent().parent().find("a[data-option='on']").parent("li").children("ul").slideUp(options.Speed / 1.2,
          function () {
            $(this).parent("li").children("a").attr("data-option", "off");
            $(this).parent("li").removeClass("show");
          })
      }
      if (a.attr("data-option") == "off") {
        a.parent("li").children("ul").slideDown(options.Speed,
          function () {
            a.attr("data-option", "on");
            a.parent('li').addClass("show");
          });
      }
      if (a.attr("data-option") == "on") {
        a.attr("data-option", "off");
        a.parent("li").children("ul").slideUp(options.Speed)
        a.parent('li').removeClass("show");
      }
    });
    if (options.autostart) {
      obj.find("a").each(function () {

        $(this).parent("li").parent("ul").slideDown(options.Speed,
          function () {
            $(this).parent("li").children("a").attr("data-option", "on");
          })
      })
    }
    else {
      obj.find("a.active").each(function () {

        $(this).parent("li").parent("ul").slideDown(options.Speed,
          function () {
            $(this).parent("li").children("a").attr("data-option", "on");
            $(this).parent('li').addClass("show");
          })
      })
    }

  }
})(window.jQuery || window.Zepto);

function CopyToClipboard(value, showNotification, notificationText) {
  var $temp = $("<input>");
  if (value != '') {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(value).select();
    document.execCommand("copy");
    $temp.remove();
  }
  if (typeof showNotification === 'undefined') {
    showNotification = true;
  }
  if (typeof notificationText === 'undefined') {
    notificationText = "Copied to clipboard";
  }
  var notificationTag = $("div.copy-notification");
  if (showNotification && notificationTag.length == 0) {
    notificationTag = $("<div/>", { "class": "copy-notification", text: notificationText });
    $("body").append(notificationTag);

    notificationTag.fadeIn("slow", function () {
      setTimeout(function () {
        notificationTag.fadeOut("slow", function () {
          notificationTag.remove();
        });
      }, 1000);
    });
  }
}

function getUrlParameter(sParam) {
  var sPageURL = window.location.search.substring(1),
    sURLVariables = sPageURL.split('&'),
    sParameterName,
    i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split('=');

    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? '' : decodeURIComponent(sParameterName[1]);
    }
  }
}

function numberWithCommas(x) {
  var parts = x.toString().split(".");
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return parts.join(".");
}
