var __getOwnPropNames = Object.getOwnPropertyNames;
var __commonJS = (cb, mod) => function __require() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
};

// app/javascript/flatpickr.js
var require_flatpickr = __commonJS({
  "app/javascript/flatpickr.js"(exports, module) {
    !function(e, n) {
      "object" == typeof exports && "undefined" != typeof module ? module.exports = n() : "function" == typeof define && define.amd ? define(n) : (e = "undefined" != typeof globalThis ? globalThis : e || self).flatpickr = n();
    }(exports, function() {
      "use strict";
      var e = function() {
        return (e = Object.assign || function(e2) {
          for (var n2, t2 = 1, a2 = arguments.length; t2 < a2; t2++) for (var i2 in n2 = arguments[t2]) Object.prototype.hasOwnProperty.call(n2, i2) && (e2[i2] = n2[i2]);
          return e2;
        }).apply(this, arguments);
      };
      function n() {
        for (var e2 = 0, n2 = 0, t2 = arguments.length; n2 < t2; n2++) e2 += arguments[n2].length;
        var a2 = Array(e2), i2 = 0;
        for (n2 = 0; n2 < t2; n2++) for (var o2 = arguments[n2], r2 = 0, l2 = o2.length; r2 < l2; r2++, i2++) a2[i2] = o2[r2];
        return a2;
      }
      var t = ["onChange", "onClose", "onDayCreate", "onDestroy", "onKeyDown", "onMonthChange", "onOpen", "onParseConfig", "onReady", "onValueUpdate", "onYearChange", "onPreCalendarPosition"], a = { _disable: [], allowInput: false, allowInvalidPreload: false, altFormat: "F j, Y", altInput: false, altInputClass: "form-control input", animate: "object" == typeof window && -1 === window.navigator.userAgent.indexOf("MSIE"), ariaDateFormat: "F j, Y", autoFillDefaultTime: true, clickOpens: true, closeOnSelect: true, conjunction: ", ", dateFormat: "Y-m-d", defaultHour: 12, defaultMinute: 0, defaultSeconds: 0, disable: [], disableMobile: false, enableSeconds: false, enableTime: false, errorHandler: function(e2) {
        return "undefined" != typeof console && console.warn(e2);
      }, getWeek: function(e2) {
        var n2 = new Date(e2.getTime());
        n2.setHours(0, 0, 0, 0), n2.setDate(n2.getDate() + 3 - (n2.getDay() + 6) % 7);
        var t2 = new Date(n2.getFullYear(), 0, 4);
        return 1 + Math.round(((n2.getTime() - t2.getTime()) / 864e5 - 3 + (t2.getDay() + 6) % 7) / 7);
      }, hourIncrement: 1, ignoredFocusElements: [], inline: false, locale: "default", minuteIncrement: 5, mode: "single", monthSelectorType: "dropdown", nextArrow: "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z' /></svg>", noCalendar: false, now: /* @__PURE__ */ new Date(), onChange: [], onClose: [], onDayCreate: [], onDestroy: [], onKeyDown: [], onMonthChange: [], onOpen: [], onParseConfig: [], onReady: [], onValueUpdate: [], onYearChange: [], onPreCalendarPosition: [], plugins: [], position: "auto", positionElement: void 0, prevArrow: "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z' /></svg>", shorthandCurrentMonth: false, showMonths: 1, static: false, time_24hr: false, weekNumbers: false, wrap: false }, i = { weekdays: { shorthand: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], longhand: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"] }, months: { shorthand: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], longhand: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"] }, daysInMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31], firstDayOfWeek: 0, ordinal: function(e2) {
        var n2 = e2 % 100;
        if (n2 > 3 && n2 < 21) return "th";
        switch (n2 % 10) {
          case 1:
            return "st";
          case 2:
            return "nd";
          case 3:
            return "rd";
          default:
            return "th";
        }
      }, rangeSeparator: " to ", weekAbbreviation: "Wk", scrollTitle: "Scroll to increment", toggleTitle: "Click to toggle", amPM: ["AM", "PM"], yearAriaLabel: "Year", monthAriaLabel: "Month", hourAriaLabel: "Hour", minuteAriaLabel: "Minute", time_24hr: false }, o = function(e2, n2) {
        return void 0 === n2 && (n2 = 2), ("000" + e2).slice(-1 * n2);
      }, r = function(e2) {
        return true === e2 ? 1 : 0;
      };
      function l(e2, n2) {
        var t2;
        return function() {
          var a2 = this, i2 = arguments;
          clearTimeout(t2), t2 = setTimeout(function() {
            return e2.apply(a2, i2);
          }, n2);
        };
      }
      var c = function(e2) {
        return e2 instanceof Array ? e2 : [e2];
      };
      function s(e2, n2, t2) {
        if (true === t2) return e2.classList.add(n2);
        e2.classList.remove(n2);
      }
      function d(e2, n2, t2) {
        var a2 = window.document.createElement(e2);
        return n2 = n2 || "", t2 = t2 || "", a2.className = n2, void 0 !== t2 && (a2.textContent = t2), a2;
      }
      function u(e2) {
        for (; e2.firstChild; ) e2.removeChild(e2.firstChild);
      }
      function f(e2, n2) {
        return n2(e2) ? e2 : e2.parentNode ? f(e2.parentNode, n2) : void 0;
      }
      function m(e2, n2) {
        var t2 = d("div", "numInputWrapper"), a2 = d("input", "numInput " + e2), i2 = d("span", "arrowUp"), o2 = d("span", "arrowDown");
        if (-1 === navigator.userAgent.indexOf("MSIE 9.0") ? a2.type = "number" : (a2.type = "text", a2.pattern = "\\d*"), void 0 !== n2) for (var r2 in n2) a2.setAttribute(r2, n2[r2]);
        return t2.appendChild(a2), t2.appendChild(i2), t2.appendChild(o2), t2;
      }
      function g(e2) {
        try {
          return "function" == typeof e2.composedPath ? e2.composedPath()[0] : e2.target;
        } catch (n2) {
          return e2.target;
        }
      }
      var p = function() {
      }, h = function(e2, n2, t2) {
        return t2.months[n2 ? "shorthand" : "longhand"][e2];
      }, v = { D: p, F: function(e2, n2, t2) {
        e2.setMonth(t2.months.longhand.indexOf(n2));
      }, G: function(e2, n2) {
        e2.setHours((e2.getHours() >= 12 ? 12 : 0) + parseFloat(n2));
      }, H: function(e2, n2) {
        e2.setHours(parseFloat(n2));
      }, J: function(e2, n2) {
        e2.setDate(parseFloat(n2));
      }, K: function(e2, n2, t2) {
        e2.setHours(e2.getHours() % 12 + 12 * r(new RegExp(t2.amPM[1], "i").test(n2)));
      }, M: function(e2, n2, t2) {
        e2.setMonth(t2.months.shorthand.indexOf(n2));
      }, S: function(e2, n2) {
        e2.setSeconds(parseFloat(n2));
      }, U: function(e2, n2) {
        return new Date(1e3 * parseFloat(n2));
      }, W: function(e2, n2, t2) {
        var a2 = parseInt(n2), i2 = new Date(e2.getFullYear(), 0, 2 + 7 * (a2 - 1), 0, 0, 0, 0);
        return i2.setDate(i2.getDate() - i2.getDay() + t2.firstDayOfWeek), i2;
      }, Y: function(e2, n2) {
        e2.setFullYear(parseFloat(n2));
      }, Z: function(e2, n2) {
        return new Date(n2);
      }, d: function(e2, n2) {
        e2.setDate(parseFloat(n2));
      }, h: function(e2, n2) {
        e2.setHours((e2.getHours() >= 12 ? 12 : 0) + parseFloat(n2));
      }, i: function(e2, n2) {
        e2.setMinutes(parseFloat(n2));
      }, j: function(e2, n2) {
        e2.setDate(parseFloat(n2));
      }, l: p, m: function(e2, n2) {
        e2.setMonth(parseFloat(n2) - 1);
      }, n: function(e2, n2) {
        e2.setMonth(parseFloat(n2) - 1);
      }, s: function(e2, n2) {
        e2.setSeconds(parseFloat(n2));
      }, u: function(e2, n2) {
        return new Date(parseFloat(n2));
      }, w: p, y: function(e2, n2) {
        e2.setFullYear(2e3 + parseFloat(n2));
      } }, D = { D: "", F: "", G: "(\\d\\d|\\d)", H: "(\\d\\d|\\d)", J: "(\\d\\d|\\d)\\w+", K: "", M: "", S: "(\\d\\d|\\d)", U: "(.+)", W: "(\\d\\d|\\d)", Y: "(\\d{4})", Z: "(.+)", d: "(\\d\\d|\\d)", h: "(\\d\\d|\\d)", i: "(\\d\\d|\\d)", j: "(\\d\\d|\\d)", l: "", m: "(\\d\\d|\\d)", n: "(\\d\\d|\\d)", s: "(\\d\\d|\\d)", u: "(.+)", w: "(\\d\\d|\\d)", y: "(\\d{2})" }, w = { Z: function(e2) {
        return e2.toISOString();
      }, D: function(e2, n2, t2) {
        return n2.weekdays.shorthand[w.w(e2, n2, t2)];
      }, F: function(e2, n2, t2) {
        return h(w.n(e2, n2, t2) - 1, false, n2);
      }, G: function(e2, n2, t2) {
        return o(w.h(e2, n2, t2));
      }, H: function(e2) {
        return o(e2.getHours());
      }, J: function(e2, n2) {
        return void 0 !== n2.ordinal ? e2.getDate() + n2.ordinal(e2.getDate()) : e2.getDate();
      }, K: function(e2, n2) {
        return n2.amPM[r(e2.getHours() > 11)];
      }, M: function(e2, n2) {
        return h(e2.getMonth(), true, n2);
      }, S: function(e2) {
        return o(e2.getSeconds());
      }, U: function(e2) {
        return e2.getTime() / 1e3;
      }, W: function(e2, n2, t2) {
        return t2.getWeek(e2);
      }, Y: function(e2) {
        return o(e2.getFullYear(), 4);
      }, d: function(e2) {
        return o(e2.getDate());
      }, h: function(e2) {
        return e2.getHours() % 12 ? e2.getHours() % 12 : 12;
      }, i: function(e2) {
        return o(e2.getMinutes());
      }, j: function(e2) {
        return e2.getDate();
      }, l: function(e2, n2) {
        return n2.weekdays.longhand[e2.getDay()];
      }, m: function(e2) {
        return o(e2.getMonth() + 1);
      }, n: function(e2) {
        return e2.getMonth() + 1;
      }, s: function(e2) {
        return e2.getSeconds();
      }, u: function(e2) {
        return e2.getTime();
      }, w: function(e2) {
        return e2.getDay();
      }, y: function(e2) {
        return String(e2.getFullYear()).substring(2);
      } }, b = function(e2) {
        var n2 = e2.config, t2 = void 0 === n2 ? a : n2, o2 = e2.l10n, r2 = void 0 === o2 ? i : o2, l2 = e2.isMobile, c2 = void 0 !== l2 && l2;
        return function(e3, n3, a2) {
          var i2 = a2 || r2;
          return void 0 === t2.formatDate || c2 ? n3.split("").map(function(n4, a3, o3) {
            return w[n4] && "\\" !== o3[a3 - 1] ? w[n4](e3, i2, t2) : "\\" !== n4 ? n4 : "";
          }).join("") : t2.formatDate(e3, n3, i2);
        };
      }, C = function(e2) {
        var n2 = e2.config, t2 = void 0 === n2 ? a : n2, o2 = e2.l10n, r2 = void 0 === o2 ? i : o2;
        return function(e3, n3, i2, o3) {
          if (0 === e3 || e3) {
            var l2, c2 = o3 || r2, s2 = e3;
            if (e3 instanceof Date) l2 = new Date(e3.getTime());
            else if ("string" != typeof e3 && void 0 !== e3.toFixed) l2 = new Date(e3);
            else if ("string" == typeof e3) {
              var d2 = n3 || (t2 || a).dateFormat, u2 = String(e3).trim();
              if ("today" === u2) l2 = /* @__PURE__ */ new Date(), i2 = true;
              else if (t2 && t2.parseDate) l2 = t2.parseDate(e3, d2);
              else if (/Z$/.test(u2) || /GMT$/.test(u2)) l2 = new Date(e3);
              else {
                for (var f2 = void 0, m2 = [], g2 = 0, p2 = 0, h2 = ""; g2 < d2.length; g2++) {
                  var w2 = d2[g2], b2 = "\\" === w2, C2 = "\\" === d2[g2 - 1] || b2;
                  if (D[w2] && !C2) {
                    h2 += D[w2];
                    var M2 = new RegExp(h2).exec(e3);
                    M2 && (f2 = true) && m2["Y" !== w2 ? "push" : "unshift"]({ fn: v[w2], val: M2[++p2] });
                  } else b2 || (h2 += ".");
                }
                l2 = t2 && t2.noCalendar ? new Date((/* @__PURE__ */ new Date()).setHours(0, 0, 0, 0)) : new Date((/* @__PURE__ */ new Date()).getFullYear(), 0, 1, 0, 0, 0, 0), m2.forEach(function(e4) {
                  var n4 = e4.fn, t3 = e4.val;
                  return l2 = n4(l2, t3, c2) || l2;
                }), l2 = f2 ? l2 : void 0;
              }
            }
            if (l2 instanceof Date && !isNaN(l2.getTime())) return true === i2 && l2.setHours(0, 0, 0, 0), l2;
            t2.errorHandler(new Error("Invalid date provided: " + s2));
          }
        };
      };
      function M(e2, n2, t2) {
        return void 0 === t2 && (t2 = true), false !== t2 ? new Date(e2.getTime()).setHours(0, 0, 0, 0) - new Date(n2.getTime()).setHours(0, 0, 0, 0) : e2.getTime() - n2.getTime();
      }
      var y = function(e2, n2, t2) {
        return 3600 * e2 + 60 * n2 + t2;
      }, x = 864e5;
      function E(e2) {
        var n2 = e2.defaultHour, t2 = e2.defaultMinute, a2 = e2.defaultSeconds;
        if (void 0 !== e2.minDate) {
          var i2 = e2.minDate.getHours(), o2 = e2.minDate.getMinutes(), r2 = e2.minDate.getSeconds();
          n2 < i2 && (n2 = i2), n2 === i2 && t2 < o2 && (t2 = o2), n2 === i2 && t2 === o2 && a2 < r2 && (a2 = e2.minDate.getSeconds());
        }
        if (void 0 !== e2.maxDate) {
          var l2 = e2.maxDate.getHours(), c2 = e2.maxDate.getMinutes();
          (n2 = Math.min(n2, l2)) === l2 && (t2 = Math.min(c2, t2)), n2 === l2 && t2 === c2 && (a2 = e2.maxDate.getSeconds());
        }
        return { hours: n2, minutes: t2, seconds: a2 };
      }
      "function" != typeof Object.assign && (Object.assign = function(e2) {
        for (var n2 = [], t2 = 1; t2 < arguments.length; t2++) n2[t2 - 1] = arguments[t2];
        if (!e2) throw TypeError("Cannot convert undefined or null to object");
        for (var a2 = function(n3) {
          n3 && Object.keys(n3).forEach(function(t3) {
            return e2[t3] = n3[t3];
          });
        }, i2 = 0, o2 = n2; i2 < o2.length; i2++) {
          var r2 = o2[i2];
          a2(r2);
        }
        return e2;
      });
      function k(p2, v2) {
        var w2 = { config: e(e({}, a), I.defaultConfig), l10n: i };
        function k2() {
          var e2;
          return (null === (e2 = w2.calendarContainer) || void 0 === e2 ? void 0 : e2.getRootNode()).activeElement || document.activeElement;
        }
        function T2(e2) {
          return e2.bind(w2);
        }
        function S() {
          var e2 = w2.config;
          false === e2.weekNumbers && 1 === e2.showMonths || true !== e2.noCalendar && window.requestAnimationFrame(function() {
            if (void 0 !== w2.calendarContainer && (w2.calendarContainer.style.visibility = "hidden", w2.calendarContainer.style.display = "block"), void 0 !== w2.daysContainer) {
              var n2 = (w2.days.offsetWidth + 1) * e2.showMonths;
              w2.daysContainer.style.width = n2 + "px", w2.calendarContainer.style.width = n2 + (void 0 !== w2.weekWrapper ? w2.weekWrapper.offsetWidth : 0) + "px", w2.calendarContainer.style.removeProperty("visibility"), w2.calendarContainer.style.removeProperty("display");
            }
          });
        }
        function _(e2) {
          if (0 === w2.selectedDates.length) {
            var n2 = void 0 === w2.config.minDate || M(/* @__PURE__ */ new Date(), w2.config.minDate) >= 0 ? /* @__PURE__ */ new Date() : new Date(w2.config.minDate.getTime()), t2 = E(w2.config);
            n2.setHours(t2.hours, t2.minutes, t2.seconds, n2.getMilliseconds()), w2.selectedDates = [n2], w2.latestSelectedDateObj = n2;
          }
          void 0 !== e2 && "blur" !== e2.type && function(e3) {
            e3.preventDefault();
            var n3 = "keydown" === e3.type, t3 = g(e3), a3 = t3;
            void 0 !== w2.amPM && t3 === w2.amPM && (w2.amPM.textContent = w2.l10n.amPM[r(w2.amPM.textContent === w2.l10n.amPM[0])]);
            var i2 = parseFloat(a3.getAttribute("min")), l2 = parseFloat(a3.getAttribute("max")), c2 = parseFloat(a3.getAttribute("step")), s2 = parseInt(a3.value, 10), d2 = e3.delta || (n3 ? 38 === e3.which ? 1 : -1 : 0), u2 = s2 + c2 * d2;
            if (void 0 !== a3.value && 2 === a3.value.length) {
              var f2 = a3 === w2.hourElement, m2 = a3 === w2.minuteElement;
              u2 < i2 ? (u2 = l2 + u2 + r(!f2) + (r(f2) && r(!w2.amPM)), m2 && L(void 0, -1, w2.hourElement)) : u2 > l2 && (u2 = a3 === w2.hourElement ? u2 - l2 - r(!w2.amPM) : i2, m2 && L(void 0, 1, w2.hourElement)), w2.amPM && f2 && (1 === c2 ? u2 + s2 === 23 : Math.abs(u2 - s2) > c2) && (w2.amPM.textContent = w2.l10n.amPM[r(w2.amPM.textContent === w2.l10n.amPM[0])]), a3.value = o(u2);
            }
          }(e2);
          var a2 = w2._input.value;
          O(), ye(), w2._input.value !== a2 && w2._debouncedChange();
        }
        function O() {
          if (void 0 !== w2.hourElement && void 0 !== w2.minuteElement) {
            var e2, n2, t2 = (parseInt(w2.hourElement.value.slice(-2), 10) || 0) % 24, a2 = (parseInt(w2.minuteElement.value, 10) || 0) % 60, i2 = void 0 !== w2.secondElement ? (parseInt(w2.secondElement.value, 10) || 0) % 60 : 0;
            void 0 !== w2.amPM && (e2 = t2, n2 = w2.amPM.textContent, t2 = e2 % 12 + 12 * r(n2 === w2.l10n.amPM[1]));
            var o2 = void 0 !== w2.config.minTime || w2.config.minDate && w2.minDateHasTime && w2.latestSelectedDateObj && 0 === M(w2.latestSelectedDateObj, w2.config.minDate, true), l2 = void 0 !== w2.config.maxTime || w2.config.maxDate && w2.maxDateHasTime && w2.latestSelectedDateObj && 0 === M(w2.latestSelectedDateObj, w2.config.maxDate, true);
            if (void 0 !== w2.config.maxTime && void 0 !== w2.config.minTime && w2.config.minTime > w2.config.maxTime) {
              var c2 = y(w2.config.minTime.getHours(), w2.config.minTime.getMinutes(), w2.config.minTime.getSeconds()), s2 = y(w2.config.maxTime.getHours(), w2.config.maxTime.getMinutes(), w2.config.maxTime.getSeconds()), d2 = y(t2, a2, i2);
              if (d2 > s2 && d2 < c2) {
                var u2 = function(e3) {
                  var n3 = Math.floor(e3 / 3600), t3 = (e3 - 3600 * n3) / 60;
                  return [n3, t3, e3 - 3600 * n3 - 60 * t3];
                }(c2);
                t2 = u2[0], a2 = u2[1], i2 = u2[2];
              }
            } else {
              if (l2) {
                var f2 = void 0 !== w2.config.maxTime ? w2.config.maxTime : w2.config.maxDate;
                (t2 = Math.min(t2, f2.getHours())) === f2.getHours() && (a2 = Math.min(a2, f2.getMinutes())), a2 === f2.getMinutes() && (i2 = Math.min(i2, f2.getSeconds()));
              }
              if (o2) {
                var m2 = void 0 !== w2.config.minTime ? w2.config.minTime : w2.config.minDate;
                (t2 = Math.max(t2, m2.getHours())) === m2.getHours() && a2 < m2.getMinutes() && (a2 = m2.getMinutes()), a2 === m2.getMinutes() && (i2 = Math.max(i2, m2.getSeconds()));
              }
            }
            A(t2, a2, i2);
          }
        }
        function F(e2) {
          var n2 = e2 || w2.latestSelectedDateObj;
          n2 && n2 instanceof Date && A(n2.getHours(), n2.getMinutes(), n2.getSeconds());
        }
        function A(e2, n2, t2) {
          void 0 !== w2.latestSelectedDateObj && w2.latestSelectedDateObj.setHours(e2 % 24, n2, t2 || 0, 0), w2.hourElement && w2.minuteElement && !w2.isMobile && (w2.hourElement.value = o(w2.config.time_24hr ? e2 : (12 + e2) % 12 + 12 * r(e2 % 12 == 0)), w2.minuteElement.value = o(n2), void 0 !== w2.amPM && (w2.amPM.textContent = w2.l10n.amPM[r(e2 >= 12)]), void 0 !== w2.secondElement && (w2.secondElement.value = o(t2)));
        }
        function N(e2) {
          var n2 = g(e2), t2 = parseInt(n2.value) + (e2.delta || 0);
          (t2 / 1e3 > 1 || "Enter" === e2.key && !/[^\d]/.test(t2.toString())) && ee(t2);
        }
        function P(e2, n2, t2, a2) {
          return n2 instanceof Array ? n2.forEach(function(n3) {
            return P(e2, n3, t2, a2);
          }) : e2 instanceof Array ? e2.forEach(function(e3) {
            return P(e3, n2, t2, a2);
          }) : (e2.addEventListener(n2, t2, a2), void w2._handlers.push({ remove: function() {
            return e2.removeEventListener(n2, t2, a2);
          } }));
        }
        function Y() {
          De("onChange");
        }
        function j(e2, n2) {
          var t2 = void 0 !== e2 ? w2.parseDate(e2) : w2.latestSelectedDateObj || (w2.config.minDate && w2.config.minDate > w2.now ? w2.config.minDate : w2.config.maxDate && w2.config.maxDate < w2.now ? w2.config.maxDate : w2.now), a2 = w2.currentYear, i2 = w2.currentMonth;
          try {
            void 0 !== t2 && (w2.currentYear = t2.getFullYear(), w2.currentMonth = t2.getMonth());
          } catch (e3) {
            e3.message = "Invalid date supplied: " + t2, w2.config.errorHandler(e3);
          }
          n2 && w2.currentYear !== a2 && (De("onYearChange"), q()), !n2 || w2.currentYear === a2 && w2.currentMonth === i2 || De("onMonthChange"), w2.redraw();
        }
        function H(e2) {
          var n2 = g(e2);
          ~n2.className.indexOf("arrow") && L(e2, n2.classList.contains("arrowUp") ? 1 : -1);
        }
        function L(e2, n2, t2) {
          var a2 = e2 && g(e2), i2 = t2 || a2 && a2.parentNode && a2.parentNode.firstChild, o2 = we("increment");
          o2.delta = n2, i2 && i2.dispatchEvent(o2);
        }
        function R(e2, n2, t2, a2) {
          var i2 = ne(n2, true), o2 = d("span", e2, n2.getDate().toString());
          return o2.dateObj = n2, o2.$i = a2, o2.setAttribute("aria-label", w2.formatDate(n2, w2.config.ariaDateFormat)), -1 === e2.indexOf("hidden") && 0 === M(n2, w2.now) && (w2.todayDateElem = o2, o2.classList.add("today"), o2.setAttribute("aria-current", "date")), i2 ? (o2.tabIndex = -1, be(n2) && (o2.classList.add("selected"), w2.selectedDateElem = o2, "range" === w2.config.mode && (s(o2, "startRange", w2.selectedDates[0] && 0 === M(n2, w2.selectedDates[0], true)), s(o2, "endRange", w2.selectedDates[1] && 0 === M(n2, w2.selectedDates[1], true)), "nextMonthDay" === e2 && o2.classList.add("inRange")))) : o2.classList.add("flatpickr-disabled"), "range" === w2.config.mode && function(e3) {
            return !("range" !== w2.config.mode || w2.selectedDates.length < 2) && (M(e3, w2.selectedDates[0]) >= 0 && M(e3, w2.selectedDates[1]) <= 0);
          }(n2) && !be(n2) && o2.classList.add("inRange"), w2.weekNumbers && 1 === w2.config.showMonths && "prevMonthDay" !== e2 && a2 % 7 == 6 && w2.weekNumbers.insertAdjacentHTML("beforeend", "<span class='flatpickr-day'>" + w2.config.getWeek(n2) + "</span>"), De("onDayCreate", o2), o2;
        }
        function W(e2) {
          e2.focus(), "range" === w2.config.mode && oe(e2);
        }
        function B(e2) {
          for (var n2 = e2 > 0 ? 0 : w2.config.showMonths - 1, t2 = e2 > 0 ? w2.config.showMonths : -1, a2 = n2; a2 != t2; a2 += e2) for (var i2 = w2.daysContainer.children[a2], o2 = e2 > 0 ? 0 : i2.children.length - 1, r2 = e2 > 0 ? i2.children.length : -1, l2 = o2; l2 != r2; l2 += e2) {
            var c2 = i2.children[l2];
            if (-1 === c2.className.indexOf("hidden") && ne(c2.dateObj)) return c2;
          }
        }
        function J(e2, n2) {
          var t2 = k2(), a2 = te(t2 || document.body), i2 = void 0 !== e2 ? e2 : a2 ? t2 : void 0 !== w2.selectedDateElem && te(w2.selectedDateElem) ? w2.selectedDateElem : void 0 !== w2.todayDateElem && te(w2.todayDateElem) ? w2.todayDateElem : B(n2 > 0 ? 1 : -1);
          void 0 === i2 ? w2._input.focus() : a2 ? function(e3, n3) {
            for (var t3 = -1 === e3.className.indexOf("Month") ? e3.dateObj.getMonth() : w2.currentMonth, a3 = n3 > 0 ? w2.config.showMonths : -1, i3 = n3 > 0 ? 1 : -1, o2 = t3 - w2.currentMonth; o2 != a3; o2 += i3) for (var r2 = w2.daysContainer.children[o2], l2 = t3 - w2.currentMonth === o2 ? e3.$i + n3 : n3 < 0 ? r2.children.length - 1 : 0, c2 = r2.children.length, s2 = l2; s2 >= 0 && s2 < c2 && s2 != (n3 > 0 ? c2 : -1); s2 += i3) {
              var d2 = r2.children[s2];
              if (-1 === d2.className.indexOf("hidden") && ne(d2.dateObj) && Math.abs(e3.$i - s2) >= Math.abs(n3)) return W(d2);
            }
            w2.changeMonth(i3), J(B(i3), 0);
          }(i2, n2) : W(i2);
        }
        function K(e2, n2) {
          for (var t2 = (new Date(e2, n2, 1).getDay() - w2.l10n.firstDayOfWeek + 7) % 7, a2 = w2.utils.getDaysInMonth((n2 - 1 + 12) % 12, e2), i2 = w2.utils.getDaysInMonth(n2, e2), o2 = window.document.createDocumentFragment(), r2 = w2.config.showMonths > 1, l2 = r2 ? "prevMonthDay hidden" : "prevMonthDay", c2 = r2 ? "nextMonthDay hidden" : "nextMonthDay", s2 = a2 + 1 - t2, u2 = 0; s2 <= a2; s2++, u2++) o2.appendChild(R("flatpickr-day " + l2, new Date(e2, n2 - 1, s2), 0, u2));
          for (s2 = 1; s2 <= i2; s2++, u2++) o2.appendChild(R("flatpickr-day", new Date(e2, n2, s2), 0, u2));
          for (var f2 = i2 + 1; f2 <= 42 - t2 && (1 === w2.config.showMonths || u2 % 7 != 0); f2++, u2++) o2.appendChild(R("flatpickr-day " + c2, new Date(e2, n2 + 1, f2 % i2), 0, u2));
          var m2 = d("div", "dayContainer");
          return m2.appendChild(o2), m2;
        }
        function U() {
          if (void 0 !== w2.daysContainer) {
            u(w2.daysContainer), w2.weekNumbers && u(w2.weekNumbers);
            for (var e2 = document.createDocumentFragment(), n2 = 0; n2 < w2.config.showMonths; n2++) {
              var t2 = new Date(w2.currentYear, w2.currentMonth, 1);
              t2.setMonth(w2.currentMonth + n2), e2.appendChild(K(t2.getFullYear(), t2.getMonth()));
            }
            w2.daysContainer.appendChild(e2), w2.days = w2.daysContainer.firstChild, "range" === w2.config.mode && 1 === w2.selectedDates.length && oe();
          }
        }
        function q() {
          if (!(w2.config.showMonths > 1 || "dropdown" !== w2.config.monthSelectorType)) {
            var e2 = function(e3) {
              return !(void 0 !== w2.config.minDate && w2.currentYear === w2.config.minDate.getFullYear() && e3 < w2.config.minDate.getMonth()) && !(void 0 !== w2.config.maxDate && w2.currentYear === w2.config.maxDate.getFullYear() && e3 > w2.config.maxDate.getMonth());
            };
            w2.monthsDropdownContainer.tabIndex = -1, w2.monthsDropdownContainer.innerHTML = "";
            for (var n2 = 0; n2 < 12; n2++) if (e2(n2)) {
              var t2 = d("option", "flatpickr-monthDropdown-month");
              t2.value = new Date(w2.currentYear, n2).getMonth().toString(), t2.textContent = h(n2, w2.config.shorthandCurrentMonth, w2.l10n), t2.tabIndex = -1, w2.currentMonth === n2 && (t2.selected = true), w2.monthsDropdownContainer.appendChild(t2);
            }
          }
        }
        function $() {
          var e2, n2 = d("div", "flatpickr-month"), t2 = window.document.createDocumentFragment();
          w2.config.showMonths > 1 || "static" === w2.config.monthSelectorType ? e2 = d("span", "cur-month") : (w2.monthsDropdownContainer = d("select", "flatpickr-monthDropdown-months"), w2.monthsDropdownContainer.setAttribute("aria-label", w2.l10n.monthAriaLabel), P(w2.monthsDropdownContainer, "change", function(e3) {
            var n3 = g(e3), t3 = parseInt(n3.value, 10);
            w2.changeMonth(t3 - w2.currentMonth), De("onMonthChange");
          }), q(), e2 = w2.monthsDropdownContainer);
          var a2 = m("cur-year", { tabindex: "-1" }), i2 = a2.getElementsByTagName("input")[0];
          i2.setAttribute("aria-label", w2.l10n.yearAriaLabel), w2.config.minDate && i2.setAttribute("min", w2.config.minDate.getFullYear().toString()), w2.config.maxDate && (i2.setAttribute("max", w2.config.maxDate.getFullYear().toString()), i2.disabled = !!w2.config.minDate && w2.config.minDate.getFullYear() === w2.config.maxDate.getFullYear());
          var o2 = d("div", "flatpickr-current-month");
          return o2.appendChild(e2), o2.appendChild(a2), t2.appendChild(o2), n2.appendChild(t2), { container: n2, yearElement: i2, monthElement: e2 };
        }
        function V() {
          u(w2.monthNav), w2.monthNav.appendChild(w2.prevMonthNav), w2.config.showMonths && (w2.yearElements = [], w2.monthElements = []);
          for (var e2 = w2.config.showMonths; e2--; ) {
            var n2 = $();
            w2.yearElements.push(n2.yearElement), w2.monthElements.push(n2.monthElement), w2.monthNav.appendChild(n2.container);
          }
          w2.monthNav.appendChild(w2.nextMonthNav);
        }
        function z() {
          w2.weekdayContainer ? u(w2.weekdayContainer) : w2.weekdayContainer = d("div", "flatpickr-weekdays");
          for (var e2 = w2.config.showMonths; e2--; ) {
            var n2 = d("div", "flatpickr-weekdaycontainer");
            w2.weekdayContainer.appendChild(n2);
          }
          return G(), w2.weekdayContainer;
        }
        function G() {
          if (w2.weekdayContainer) {
            var e2 = w2.l10n.firstDayOfWeek, t2 = n(w2.l10n.weekdays.shorthand);
            e2 > 0 && e2 < t2.length && (t2 = n(t2.splice(e2, t2.length), t2.splice(0, e2)));
            for (var a2 = w2.config.showMonths; a2--; ) w2.weekdayContainer.children[a2].innerHTML = "\n      <span class='flatpickr-weekday'>\n        " + t2.join("</span><span class='flatpickr-weekday'>") + "\n      </span>\n      ";
          }
        }
        function Z(e2, n2) {
          void 0 === n2 && (n2 = true);
          var t2 = n2 ? e2 : e2 - w2.currentMonth;
          t2 < 0 && true === w2._hidePrevMonthArrow || t2 > 0 && true === w2._hideNextMonthArrow || (w2.currentMonth += t2, (w2.currentMonth < 0 || w2.currentMonth > 11) && (w2.currentYear += w2.currentMonth > 11 ? 1 : -1, w2.currentMonth = (w2.currentMonth + 12) % 12, De("onYearChange"), q()), U(), De("onMonthChange"), Ce());
        }
        function Q(e2) {
          return w2.calendarContainer.contains(e2);
        }
        function X(e2) {
          if (w2.isOpen && !w2.config.inline) {
            var n2 = g(e2), t2 = Q(n2), a2 = !(n2 === w2.input || n2 === w2.altInput || w2.element.contains(n2) || e2.path && e2.path.indexOf && (~e2.path.indexOf(w2.input) || ~e2.path.indexOf(w2.altInput))) && !t2 && !Q(e2.relatedTarget), i2 = !w2.config.ignoredFocusElements.some(function(e3) {
              return e3.contains(n2);
            });
            a2 && i2 && (w2.config.allowInput && w2.setDate(w2._input.value, false, w2.config.altInput ? w2.config.altFormat : w2.config.dateFormat), void 0 !== w2.timeContainer && void 0 !== w2.minuteElement && void 0 !== w2.hourElement && "" !== w2.input.value && void 0 !== w2.input.value && _(), w2.close(), w2.config && "range" === w2.config.mode && 1 === w2.selectedDates.length && w2.clear(false));
          }
        }
        function ee(e2) {
          if (!(!e2 || w2.config.minDate && e2 < w2.config.minDate.getFullYear() || w2.config.maxDate && e2 > w2.config.maxDate.getFullYear())) {
            var n2 = e2, t2 = w2.currentYear !== n2;
            w2.currentYear = n2 || w2.currentYear, w2.config.maxDate && w2.currentYear === w2.config.maxDate.getFullYear() ? w2.currentMonth = Math.min(w2.config.maxDate.getMonth(), w2.currentMonth) : w2.config.minDate && w2.currentYear === w2.config.minDate.getFullYear() && (w2.currentMonth = Math.max(w2.config.minDate.getMonth(), w2.currentMonth)), t2 && (w2.redraw(), De("onYearChange"), q());
          }
        }
        function ne(e2, n2) {
          var t2;
          void 0 === n2 && (n2 = true);
          var a2 = w2.parseDate(e2, void 0, n2);
          if (w2.config.minDate && a2 && M(a2, w2.config.minDate, void 0 !== n2 ? n2 : !w2.minDateHasTime) < 0 || w2.config.maxDate && a2 && M(a2, w2.config.maxDate, void 0 !== n2 ? n2 : !w2.maxDateHasTime) > 0) return false;
          if (!w2.config.enable && 0 === w2.config.disable.length) return true;
          if (void 0 === a2) return false;
          for (var i2 = !!w2.config.enable, o2 = null !== (t2 = w2.config.enable) && void 0 !== t2 ? t2 : w2.config.disable, r2 = 0, l2 = void 0; r2 < o2.length; r2++) {
            if ("function" == typeof (l2 = o2[r2]) && l2(a2)) return i2;
            if (l2 instanceof Date && void 0 !== a2 && l2.getTime() === a2.getTime()) return i2;
            if ("string" == typeof l2) {
              var c2 = w2.parseDate(l2, void 0, true);
              return c2 && c2.getTime() === a2.getTime() ? i2 : !i2;
            }
            if ("object" == typeof l2 && void 0 !== a2 && l2.from && l2.to && a2.getTime() >= l2.from.getTime() && a2.getTime() <= l2.to.getTime()) return i2;
          }
          return !i2;
        }
        function te(e2) {
          return void 0 !== w2.daysContainer && (-1 === e2.className.indexOf("hidden") && -1 === e2.className.indexOf("flatpickr-disabled") && w2.daysContainer.contains(e2));
        }
        function ae(e2) {
          var n2 = e2.target === w2._input, t2 = w2._input.value.trimEnd() !== Me();
          !n2 || !t2 || e2.relatedTarget && Q(e2.relatedTarget) || w2.setDate(w2._input.value, true, e2.target === w2.altInput ? w2.config.altFormat : w2.config.dateFormat);
        }
        function ie(e2) {
          var n2 = g(e2), t2 = w2.config.wrap ? p2.contains(n2) : n2 === w2._input, a2 = w2.config.allowInput, i2 = w2.isOpen && (!a2 || !t2), o2 = w2.config.inline && t2 && !a2;
          if (13 === e2.keyCode && t2) {
            if (a2) return w2.setDate(w2._input.value, true, n2 === w2.altInput ? w2.config.altFormat : w2.config.dateFormat), w2.close(), n2.blur();
            w2.open();
          } else if (Q(n2) || i2 || o2) {
            var r2 = !!w2.timeContainer && w2.timeContainer.contains(n2);
            switch (e2.keyCode) {
              case 13:
                r2 ? (e2.preventDefault(), _(), fe()) : me(e2);
                break;
              case 27:
                e2.preventDefault(), fe();
                break;
              case 8:
              case 46:
                t2 && !w2.config.allowInput && (e2.preventDefault(), w2.clear());
                break;
              case 37:
              case 39:
                if (r2 || t2) w2.hourElement && w2.hourElement.focus();
                else {
                  e2.preventDefault();
                  var l2 = k2();
                  if (void 0 !== w2.daysContainer && (false === a2 || l2 && te(l2))) {
                    var c2 = 39 === e2.keyCode ? 1 : -1;
                    e2.ctrlKey ? (e2.stopPropagation(), Z(c2), J(B(1), 0)) : J(void 0, c2);
                  }
                }
                break;
              case 38:
              case 40:
                e2.preventDefault();
                var s2 = 40 === e2.keyCode ? 1 : -1;
                w2.daysContainer && void 0 !== n2.$i || n2 === w2.input || n2 === w2.altInput ? e2.ctrlKey ? (e2.stopPropagation(), ee(w2.currentYear - s2), J(B(1), 0)) : r2 || J(void 0, 7 * s2) : n2 === w2.currentYearElement ? ee(w2.currentYear - s2) : w2.config.enableTime && (!r2 && w2.hourElement && w2.hourElement.focus(), _(e2), w2._debouncedChange());
                break;
              case 9:
                if (r2) {
                  var d2 = [w2.hourElement, w2.minuteElement, w2.secondElement, w2.amPM].concat(w2.pluginElements).filter(function(e3) {
                    return e3;
                  }), u2 = d2.indexOf(n2);
                  if (-1 !== u2) {
                    var f2 = d2[u2 + (e2.shiftKey ? -1 : 1)];
                    e2.preventDefault(), (f2 || w2._input).focus();
                  }
                } else !w2.config.noCalendar && w2.daysContainer && w2.daysContainer.contains(n2) && e2.shiftKey && (e2.preventDefault(), w2._input.focus());
            }
          }
          if (void 0 !== w2.amPM && n2 === w2.amPM) switch (e2.key) {
            case w2.l10n.amPM[0].charAt(0):
            case w2.l10n.amPM[0].charAt(0).toLowerCase():
              w2.amPM.textContent = w2.l10n.amPM[0], O(), ye();
              break;
            case w2.l10n.amPM[1].charAt(0):
            case w2.l10n.amPM[1].charAt(0).toLowerCase():
              w2.amPM.textContent = w2.l10n.amPM[1], O(), ye();
          }
          (t2 || Q(n2)) && De("onKeyDown", e2);
        }
        function oe(e2, n2) {
          if (void 0 === n2 && (n2 = "flatpickr-day"), 1 === w2.selectedDates.length && (!e2 || e2.classList.contains(n2) && !e2.classList.contains("flatpickr-disabled"))) {
            for (var t2 = e2 ? e2.dateObj.getTime() : w2.days.firstElementChild.dateObj.getTime(), a2 = w2.parseDate(w2.selectedDates[0], void 0, true).getTime(), i2 = Math.min(t2, w2.selectedDates[0].getTime()), o2 = Math.max(t2, w2.selectedDates[0].getTime()), r2 = false, l2 = 0, c2 = 0, s2 = i2; s2 < o2; s2 += x) ne(new Date(s2), true) || (r2 = r2 || s2 > i2 && s2 < o2, s2 < a2 && (!l2 || s2 > l2) ? l2 = s2 : s2 > a2 && (!c2 || s2 < c2) && (c2 = s2));
            Array.from(w2.rContainer.querySelectorAll("*:nth-child(-n+" + w2.config.showMonths + ") > ." + n2)).forEach(function(n3) {
              var i3, o3, s3, d2 = n3.dateObj.getTime(), u2 = l2 > 0 && d2 < l2 || c2 > 0 && d2 > c2;
              if (u2) return n3.classList.add("notAllowed"), void ["inRange", "startRange", "endRange"].forEach(function(e3) {
                n3.classList.remove(e3);
              });
              r2 && !u2 || (["startRange", "inRange", "endRange", "notAllowed"].forEach(function(e3) {
                n3.classList.remove(e3);
              }), void 0 !== e2 && (e2.classList.add(t2 <= w2.selectedDates[0].getTime() ? "startRange" : "endRange"), a2 < t2 && d2 === a2 ? n3.classList.add("startRange") : a2 > t2 && d2 === a2 && n3.classList.add("endRange"), d2 >= l2 && (0 === c2 || d2 <= c2) && (o3 = a2, s3 = t2, (i3 = d2) > Math.min(o3, s3) && i3 < Math.max(o3, s3)) && n3.classList.add("inRange")));
            });
          }
        }
        function re() {
          !w2.isOpen || w2.config.static || w2.config.inline || de();
        }
        function le(e2) {
          return function(n2) {
            var t2 = w2.config["_" + e2 + "Date"] = w2.parseDate(n2, w2.config.dateFormat), a2 = w2.config["_" + ("min" === e2 ? "max" : "min") + "Date"];
            void 0 !== t2 && (w2["min" === e2 ? "minDateHasTime" : "maxDateHasTime"] = t2.getHours() > 0 || t2.getMinutes() > 0 || t2.getSeconds() > 0), w2.selectedDates && (w2.selectedDates = w2.selectedDates.filter(function(e3) {
              return ne(e3);
            }), w2.selectedDates.length || "min" !== e2 || F(t2), ye()), w2.daysContainer && (ue(), void 0 !== t2 ? w2.currentYearElement[e2] = t2.getFullYear().toString() : w2.currentYearElement.removeAttribute(e2), w2.currentYearElement.disabled = !!a2 && void 0 !== t2 && a2.getFullYear() === t2.getFullYear());
          };
        }
        function ce() {
          return w2.config.wrap ? p2.querySelector("[data-input]") : p2;
        }
        function se() {
          "object" != typeof w2.config.locale && void 0 === I.l10ns[w2.config.locale] && w2.config.errorHandler(new Error("flatpickr: invalid locale " + w2.config.locale)), w2.l10n = e(e({}, I.l10ns.default), "object" == typeof w2.config.locale ? w2.config.locale : "default" !== w2.config.locale ? I.l10ns[w2.config.locale] : void 0), D.D = "(" + w2.l10n.weekdays.shorthand.join("|") + ")", D.l = "(" + w2.l10n.weekdays.longhand.join("|") + ")", D.M = "(" + w2.l10n.months.shorthand.join("|") + ")", D.F = "(" + w2.l10n.months.longhand.join("|") + ")", D.K = "(" + w2.l10n.amPM[0] + "|" + w2.l10n.amPM[1] + "|" + w2.l10n.amPM[0].toLowerCase() + "|" + w2.l10n.amPM[1].toLowerCase() + ")", void 0 === e(e({}, v2), JSON.parse(JSON.stringify(p2.dataset || {}))).time_24hr && void 0 === I.defaultConfig.time_24hr && (w2.config.time_24hr = w2.l10n.time_24hr), w2.formatDate = b(w2), w2.parseDate = C({ config: w2.config, l10n: w2.l10n });
        }
        function de(e2) {
          if ("function" != typeof w2.config.position) {
            if (void 0 !== w2.calendarContainer) {
              De("onPreCalendarPosition");
              var n2 = e2 || w2._positionElement, t2 = Array.prototype.reduce.call(w2.calendarContainer.children, function(e3, n3) {
                return e3 + n3.offsetHeight;
              }, 0), a2 = w2.calendarContainer.offsetWidth, i2 = w2.config.position.split(" "), o2 = i2[0], r2 = i2.length > 1 ? i2[1] : null, l2 = n2.getBoundingClientRect(), c2 = window.innerHeight - l2.bottom, d2 = "above" === o2 || "below" !== o2 && c2 < t2 && l2.top > t2, u2 = window.pageYOffset + l2.top + (d2 ? -t2 - 2 : n2.offsetHeight + 2);
              if (s(w2.calendarContainer, "arrowTop", !d2), s(w2.calendarContainer, "arrowBottom", d2), !w2.config.inline) {
                var f2 = window.pageXOffset + l2.left, m2 = false, g2 = false;
                "center" === r2 ? (f2 -= (a2 - l2.width) / 2, m2 = true) : "right" === r2 && (f2 -= a2 - l2.width, g2 = true), s(w2.calendarContainer, "arrowLeft", !m2 && !g2), s(w2.calendarContainer, "arrowCenter", m2), s(w2.calendarContainer, "arrowRight", g2);
                var p3 = window.document.body.offsetWidth - (window.pageXOffset + l2.right), h2 = f2 + a2 > window.document.body.offsetWidth, v3 = p3 + a2 > window.document.body.offsetWidth;
                if (s(w2.calendarContainer, "rightMost", h2), !w2.config.static) if (w2.calendarContainer.style.top = u2 + "px", h2) if (v3) {
                  var D2 = function() {
                    for (var e3 = null, n3 = 0; n3 < document.styleSheets.length; n3++) {
                      var t3 = document.styleSheets[n3];
                      if (t3.cssRules) {
                        try {
                          t3.cssRules;
                        } catch (e4) {
                          continue;
                        }
                        e3 = t3;
                        break;
                      }
                    }
                    return null != e3 ? e3 : (a3 = document.createElement("style"), document.head.appendChild(a3), a3.sheet);
                    var a3;
                  }();
                  if (void 0 === D2) return;
                  var b2 = window.document.body.offsetWidth, C2 = Math.max(0, b2 / 2 - a2 / 2), M2 = D2.cssRules.length, y2 = "{left:" + l2.left + "px;right:auto;}";
                  s(w2.calendarContainer, "rightMost", false), s(w2.calendarContainer, "centerMost", true), D2.insertRule(".flatpickr-calendar.centerMost:before,.flatpickr-calendar.centerMost:after" + y2, M2), w2.calendarContainer.style.left = C2 + "px", w2.calendarContainer.style.right = "auto";
                } else w2.calendarContainer.style.left = "auto", w2.calendarContainer.style.right = p3 + "px";
                else w2.calendarContainer.style.left = f2 + "px", w2.calendarContainer.style.right = "auto";
              }
            }
          } else w2.config.position(w2, e2);
        }
        function ue() {
          w2.config.noCalendar || w2.isMobile || (q(), Ce(), U());
        }
        function fe() {
          w2._input.focus(), -1 !== window.navigator.userAgent.indexOf("MSIE") || void 0 !== navigator.msMaxTouchPoints ? setTimeout(w2.close, 0) : w2.close();
        }
        function me(e2) {
          e2.preventDefault(), e2.stopPropagation();
          var n2 = f(g(e2), function(e3) {
            return e3.classList && e3.classList.contains("flatpickr-day") && !e3.classList.contains("flatpickr-disabled") && !e3.classList.contains("notAllowed");
          });
          if (void 0 !== n2) {
            var t2 = n2, a2 = w2.latestSelectedDateObj = new Date(t2.dateObj.getTime()), i2 = (a2.getMonth() < w2.currentMonth || a2.getMonth() > w2.currentMonth + w2.config.showMonths - 1) && "range" !== w2.config.mode;
            if (w2.selectedDateElem = t2, "single" === w2.config.mode) w2.selectedDates = [a2];
            else if ("multiple" === w2.config.mode) {
              var o2 = be(a2);
              o2 ? w2.selectedDates.splice(parseInt(o2), 1) : w2.selectedDates.push(a2);
            } else "range" === w2.config.mode && (2 === w2.selectedDates.length && w2.clear(false, false), w2.latestSelectedDateObj = a2, w2.selectedDates.push(a2), 0 !== M(a2, w2.selectedDates[0], true) && w2.selectedDates.sort(function(e3, n3) {
              return e3.getTime() - n3.getTime();
            }));
            if (O(), i2) {
              var r2 = w2.currentYear !== a2.getFullYear();
              w2.currentYear = a2.getFullYear(), w2.currentMonth = a2.getMonth(), r2 && (De("onYearChange"), q()), De("onMonthChange");
            }
            if (Ce(), U(), ye(), i2 || "range" === w2.config.mode || 1 !== w2.config.showMonths ? void 0 !== w2.selectedDateElem && void 0 === w2.hourElement && w2.selectedDateElem && w2.selectedDateElem.focus() : W(t2), void 0 !== w2.hourElement && void 0 !== w2.hourElement && w2.hourElement.focus(), w2.config.closeOnSelect) {
              var l2 = "single" === w2.config.mode && !w2.config.enableTime, c2 = "range" === w2.config.mode && 2 === w2.selectedDates.length && !w2.config.enableTime;
              (l2 || c2) && fe();
            }
            Y();
          }
        }
        w2.parseDate = C({ config: w2.config, l10n: w2.l10n }), w2._handlers = [], w2.pluginElements = [], w2.loadedPlugins = [], w2._bind = P, w2._setHoursFromDate = F, w2._positionCalendar = de, w2.changeMonth = Z, w2.changeYear = ee, w2.clear = function(e2, n2) {
          void 0 === e2 && (e2 = true);
          void 0 === n2 && (n2 = true);
          w2.input.value = "", void 0 !== w2.altInput && (w2.altInput.value = "");
          void 0 !== w2.mobileInput && (w2.mobileInput.value = "");
          w2.selectedDates = [], w2.latestSelectedDateObj = void 0, true === n2 && (w2.currentYear = w2._initialDate.getFullYear(), w2.currentMonth = w2._initialDate.getMonth());
          if (true === w2.config.enableTime) {
            var t2 = E(w2.config), a2 = t2.hours, i2 = t2.minutes, o2 = t2.seconds;
            A(a2, i2, o2);
          }
          w2.redraw(), e2 && De("onChange");
        }, w2.close = function() {
          w2.isOpen = false, w2.isMobile || (void 0 !== w2.calendarContainer && w2.calendarContainer.classList.remove("open"), void 0 !== w2._input && w2._input.classList.remove("active"));
          De("onClose");
        }, w2.onMouseOver = oe, w2._createElement = d, w2.createDay = R, w2.destroy = function() {
          void 0 !== w2.config && De("onDestroy");
          for (var e2 = w2._handlers.length; e2--; ) w2._handlers[e2].remove();
          if (w2._handlers = [], w2.mobileInput) w2.mobileInput.parentNode && w2.mobileInput.parentNode.removeChild(w2.mobileInput), w2.mobileInput = void 0;
          else if (w2.calendarContainer && w2.calendarContainer.parentNode) if (w2.config.static && w2.calendarContainer.parentNode) {
            var n2 = w2.calendarContainer.parentNode;
            if (n2.lastChild && n2.removeChild(n2.lastChild), n2.parentNode) {
              for (; n2.firstChild; ) n2.parentNode.insertBefore(n2.firstChild, n2);
              n2.parentNode.removeChild(n2);
            }
          } else w2.calendarContainer.parentNode.removeChild(w2.calendarContainer);
          w2.altInput && (w2.input.type = "text", w2.altInput.parentNode && w2.altInput.parentNode.removeChild(w2.altInput), delete w2.altInput);
          w2.input && (w2.input.type = w2.input._type, w2.input.classList.remove("flatpickr-input"), w2.input.removeAttribute("readonly"));
          ["_showTimeInput", "latestSelectedDateObj", "_hideNextMonthArrow", "_hidePrevMonthArrow", "__hideNextMonthArrow", "__hidePrevMonthArrow", "isMobile", "isOpen", "selectedDateElem", "minDateHasTime", "maxDateHasTime", "days", "daysContainer", "_input", "_positionElement", "innerContainer", "rContainer", "monthNav", "todayDateElem", "calendarContainer", "weekdayContainer", "prevMonthNav", "nextMonthNav", "monthsDropdownContainer", "currentMonthElement", "currentYearElement", "navigationCurrentMonth", "selectedDateElem", "config"].forEach(function(e3) {
            try {
              delete w2[e3];
            } catch (e4) {
            }
          });
        }, w2.isEnabled = ne, w2.jumpToDate = j, w2.updateValue = ye, w2.open = function(e2, n2) {
          void 0 === n2 && (n2 = w2._positionElement);
          if (true === w2.isMobile) {
            if (e2) {
              e2.preventDefault();
              var t2 = g(e2);
              t2 && t2.blur();
            }
            return void 0 !== w2.mobileInput && (w2.mobileInput.focus(), w2.mobileInput.click()), void De("onOpen");
          }
          if (w2._input.disabled || w2.config.inline) return;
          var a2 = w2.isOpen;
          w2.isOpen = true, a2 || (w2.calendarContainer.classList.add("open"), w2._input.classList.add("active"), De("onOpen"), de(n2));
          true === w2.config.enableTime && true === w2.config.noCalendar && (false !== w2.config.allowInput || void 0 !== e2 && w2.timeContainer.contains(e2.relatedTarget) || setTimeout(function() {
            return w2.hourElement.select();
          }, 50));
        }, w2.redraw = ue, w2.set = function(e2, n2) {
          if (null !== e2 && "object" == typeof e2) for (var a2 in Object.assign(w2.config, e2), e2) void 0 !== ge[a2] && ge[a2].forEach(function(e3) {
            return e3();
          });
          else w2.config[e2] = n2, void 0 !== ge[e2] ? ge[e2].forEach(function(e3) {
            return e3();
          }) : t.indexOf(e2) > -1 && (w2.config[e2] = c(n2));
          w2.redraw(), ye(true);
        }, w2.setDate = function(e2, n2, t2) {
          void 0 === n2 && (n2 = false);
          void 0 === t2 && (t2 = w2.config.dateFormat);
          if (0 !== e2 && !e2 || e2 instanceof Array && 0 === e2.length) return w2.clear(n2);
          pe(e2, t2), w2.latestSelectedDateObj = w2.selectedDates[w2.selectedDates.length - 1], w2.redraw(), j(void 0, n2), F(), 0 === w2.selectedDates.length && w2.clear(false);
          ye(n2), n2 && De("onChange");
        }, w2.toggle = function(e2) {
          if (true === w2.isOpen) return w2.close();
          w2.open(e2);
        };
        var ge = { locale: [se, G], showMonths: [V, S, z], minDate: [j], maxDate: [j], positionElement: [ve], clickOpens: [function() {
          true === w2.config.clickOpens ? (P(w2._input, "focus", w2.open), P(w2._input, "click", w2.open)) : (w2._input.removeEventListener("focus", w2.open), w2._input.removeEventListener("click", w2.open));
        }] };
        function pe(e2, n2) {
          var t2 = [];
          if (e2 instanceof Array) t2 = e2.map(function(e3) {
            return w2.parseDate(e3, n2);
          });
          else if (e2 instanceof Date || "number" == typeof e2) t2 = [w2.parseDate(e2, n2)];
          else if ("string" == typeof e2) switch (w2.config.mode) {
            case "single":
            case "time":
              t2 = [w2.parseDate(e2, n2)];
              break;
            case "multiple":
              t2 = e2.split(w2.config.conjunction).map(function(e3) {
                return w2.parseDate(e3, n2);
              });
              break;
            case "range":
              t2 = e2.split(w2.l10n.rangeSeparator).map(function(e3) {
                return w2.parseDate(e3, n2);
              });
          }
          else w2.config.errorHandler(new Error("Invalid date supplied: " + JSON.stringify(e2)));
          w2.selectedDates = w2.config.allowInvalidPreload ? t2 : t2.filter(function(e3) {
            return e3 instanceof Date && ne(e3, false);
          }), "range" === w2.config.mode && w2.selectedDates.sort(function(e3, n3) {
            return e3.getTime() - n3.getTime();
          });
        }
        function he(e2) {
          return e2.slice().map(function(e3) {
            return "string" == typeof e3 || "number" == typeof e3 || e3 instanceof Date ? w2.parseDate(e3, void 0, true) : e3 && "object" == typeof e3 && e3.from && e3.to ? { from: w2.parseDate(e3.from, void 0), to: w2.parseDate(e3.to, void 0) } : e3;
          }).filter(function(e3) {
            return e3;
          });
        }
        function ve() {
          w2._positionElement = w2.config.positionElement || w2._input;
        }
        function De(e2, n2) {
          if (void 0 !== w2.config) {
            var t2 = w2.config[e2];
            if (void 0 !== t2 && t2.length > 0) for (var a2 = 0; t2[a2] && a2 < t2.length; a2++) t2[a2](w2.selectedDates, w2.input.value, w2, n2);
            "onChange" === e2 && (w2.input.dispatchEvent(we("change")), w2.input.dispatchEvent(we("input")));
          }
        }
        function we(e2) {
          var n2 = document.createEvent("Event");
          return n2.initEvent(e2, true, true), n2;
        }
        function be(e2) {
          for (var n2 = 0; n2 < w2.selectedDates.length; n2++) {
            var t2 = w2.selectedDates[n2];
            if (t2 instanceof Date && 0 === M(t2, e2)) return "" + n2;
          }
          return false;
        }
        function Ce() {
          w2.config.noCalendar || w2.isMobile || !w2.monthNav || (w2.yearElements.forEach(function(e2, n2) {
            var t2 = new Date(w2.currentYear, w2.currentMonth, 1);
            t2.setMonth(w2.currentMonth + n2), w2.config.showMonths > 1 || "static" === w2.config.monthSelectorType ? w2.monthElements[n2].textContent = h(t2.getMonth(), w2.config.shorthandCurrentMonth, w2.l10n) + " " : w2.monthsDropdownContainer.value = t2.getMonth().toString(), e2.value = t2.getFullYear().toString();
          }), w2._hidePrevMonthArrow = void 0 !== w2.config.minDate && (w2.currentYear === w2.config.minDate.getFullYear() ? w2.currentMonth <= w2.config.minDate.getMonth() : w2.currentYear < w2.config.minDate.getFullYear()), w2._hideNextMonthArrow = void 0 !== w2.config.maxDate && (w2.currentYear === w2.config.maxDate.getFullYear() ? w2.currentMonth + 1 > w2.config.maxDate.getMonth() : w2.currentYear > w2.config.maxDate.getFullYear()));
        }
        function Me(e2) {
          var n2 = e2 || (w2.config.altInput ? w2.config.altFormat : w2.config.dateFormat);
          return w2.selectedDates.map(function(e3) {
            return w2.formatDate(e3, n2);
          }).filter(function(e3, n3, t2) {
            return "range" !== w2.config.mode || w2.config.enableTime || t2.indexOf(e3) === n3;
          }).join("range" !== w2.config.mode ? w2.config.conjunction : w2.l10n.rangeSeparator);
        }
        function ye(e2) {
          void 0 === e2 && (e2 = true), void 0 !== w2.mobileInput && w2.mobileFormatStr && (w2.mobileInput.value = void 0 !== w2.latestSelectedDateObj ? w2.formatDate(w2.latestSelectedDateObj, w2.mobileFormatStr) : ""), w2.input.value = Me(w2.config.dateFormat), void 0 !== w2.altInput && (w2.altInput.value = Me(w2.config.altFormat)), false !== e2 && De("onValueUpdate");
        }
        function xe(e2) {
          var n2 = g(e2), t2 = w2.prevMonthNav.contains(n2), a2 = w2.nextMonthNav.contains(n2);
          t2 || a2 ? Z(t2 ? -1 : 1) : w2.yearElements.indexOf(n2) >= 0 ? n2.select() : n2.classList.contains("arrowUp") ? w2.changeYear(w2.currentYear + 1) : n2.classList.contains("arrowDown") && w2.changeYear(w2.currentYear - 1);
        }
        return function() {
          w2.element = w2.input = p2, w2.isOpen = false, function() {
            var n3 = ["wrap", "weekNumbers", "allowInput", "allowInvalidPreload", "clickOpens", "time_24hr", "enableTime", "noCalendar", "altInput", "shorthandCurrentMonth", "inline", "static", "enableSeconds", "disableMobile"], i2 = e(e({}, JSON.parse(JSON.stringify(p2.dataset || {}))), v2), o2 = {};
            w2.config.parseDate = i2.parseDate, w2.config.formatDate = i2.formatDate, Object.defineProperty(w2.config, "enable", { get: function() {
              return w2.config._enable;
            }, set: function(e2) {
              w2.config._enable = he(e2);
            } }), Object.defineProperty(w2.config, "disable", { get: function() {
              return w2.config._disable;
            }, set: function(e2) {
              w2.config._disable = he(e2);
            } });
            var r2 = "time" === i2.mode;
            if (!i2.dateFormat && (i2.enableTime || r2)) {
              var l2 = I.defaultConfig.dateFormat || a.dateFormat;
              o2.dateFormat = i2.noCalendar || r2 ? "H:i" + (i2.enableSeconds ? ":S" : "") : l2 + " H:i" + (i2.enableSeconds ? ":S" : "");
            }
            if (i2.altInput && (i2.enableTime || r2) && !i2.altFormat) {
              var s2 = I.defaultConfig.altFormat || a.altFormat;
              o2.altFormat = i2.noCalendar || r2 ? "h:i" + (i2.enableSeconds ? ":S K" : " K") : s2 + " h:i" + (i2.enableSeconds ? ":S" : "") + " K";
            }
            Object.defineProperty(w2.config, "minDate", { get: function() {
              return w2.config._minDate;
            }, set: le("min") }), Object.defineProperty(w2.config, "maxDate", { get: function() {
              return w2.config._maxDate;
            }, set: le("max") });
            var d2 = function(e2) {
              return function(n4) {
                w2.config["min" === e2 ? "_minTime" : "_maxTime"] = w2.parseDate(n4, "H:i:S");
              };
            };
            Object.defineProperty(w2.config, "minTime", { get: function() {
              return w2.config._minTime;
            }, set: d2("min") }), Object.defineProperty(w2.config, "maxTime", { get: function() {
              return w2.config._maxTime;
            }, set: d2("max") }), "time" === i2.mode && (w2.config.noCalendar = true, w2.config.enableTime = true);
            Object.assign(w2.config, o2, i2);
            for (var u2 = 0; u2 < n3.length; u2++) w2.config[n3[u2]] = true === w2.config[n3[u2]] || "true" === w2.config[n3[u2]];
            t.filter(function(e2) {
              return void 0 !== w2.config[e2];
            }).forEach(function(e2) {
              w2.config[e2] = c(w2.config[e2] || []).map(T2);
            }), w2.isMobile = !w2.config.disableMobile && !w2.config.inline && "single" === w2.config.mode && !w2.config.disable.length && !w2.config.enable && !w2.config.weekNumbers && /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            for (u2 = 0; u2 < w2.config.plugins.length; u2++) {
              var f2 = w2.config.plugins[u2](w2) || {};
              for (var m2 in f2) t.indexOf(m2) > -1 ? w2.config[m2] = c(f2[m2]).map(T2).concat(w2.config[m2]) : void 0 === i2[m2] && (w2.config[m2] = f2[m2]);
            }
            i2.altInputClass || (w2.config.altInputClass = ce().className + " " + w2.config.altInputClass);
            De("onParseConfig");
          }(), se(), function() {
            if (w2.input = ce(), !w2.input) return void w2.config.errorHandler(new Error("Invalid input element specified"));
            w2.input._type = w2.input.type, w2.input.type = "text", w2.input.classList.add("flatpickr-input"), w2._input = w2.input, w2.config.altInput && (w2.altInput = d(w2.input.nodeName, w2.config.altInputClass), w2._input = w2.altInput, w2.altInput.placeholder = w2.input.placeholder, w2.altInput.disabled = w2.input.disabled, w2.altInput.required = w2.input.required, w2.altInput.tabIndex = w2.input.tabIndex, w2.altInput.type = "text", w2.input.setAttribute("type", "hidden"), !w2.config.static && w2.input.parentNode && w2.input.parentNode.insertBefore(w2.altInput, w2.input.nextSibling));
            w2.config.allowInput || w2._input.setAttribute("readonly", "readonly");
            ve();
          }(), function() {
            w2.selectedDates = [], w2.now = w2.parseDate(w2.config.now) || /* @__PURE__ */ new Date();
            var e2 = w2.config.defaultDate || ("INPUT" !== w2.input.nodeName && "TEXTAREA" !== w2.input.nodeName || !w2.input.placeholder || w2.input.value !== w2.input.placeholder ? w2.input.value : null);
            e2 && pe(e2, w2.config.dateFormat);
            w2._initialDate = w2.selectedDates.length > 0 ? w2.selectedDates[0] : w2.config.minDate && w2.config.minDate.getTime() > w2.now.getTime() ? w2.config.minDate : w2.config.maxDate && w2.config.maxDate.getTime() < w2.now.getTime() ? w2.config.maxDate : w2.now, w2.currentYear = w2._initialDate.getFullYear(), w2.currentMonth = w2._initialDate.getMonth(), w2.selectedDates.length > 0 && (w2.latestSelectedDateObj = w2.selectedDates[0]);
            void 0 !== w2.config.minTime && (w2.config.minTime = w2.parseDate(w2.config.minTime, "H:i"));
            void 0 !== w2.config.maxTime && (w2.config.maxTime = w2.parseDate(w2.config.maxTime, "H:i"));
            w2.minDateHasTime = !!w2.config.minDate && (w2.config.minDate.getHours() > 0 || w2.config.minDate.getMinutes() > 0 || w2.config.minDate.getSeconds() > 0), w2.maxDateHasTime = !!w2.config.maxDate && (w2.config.maxDate.getHours() > 0 || w2.config.maxDate.getMinutes() > 0 || w2.config.maxDate.getSeconds() > 0);
          }(), w2.utils = { getDaysInMonth: function(e2, n3) {
            return void 0 === e2 && (e2 = w2.currentMonth), void 0 === n3 && (n3 = w2.currentYear), 1 === e2 && (n3 % 4 == 0 && n3 % 100 != 0 || n3 % 400 == 0) ? 29 : w2.l10n.daysInMonth[e2];
          } }, w2.isMobile || function() {
            var e2 = window.document.createDocumentFragment();
            if (w2.calendarContainer = d("div", "flatpickr-calendar"), w2.calendarContainer.tabIndex = -1, !w2.config.noCalendar) {
              if (e2.appendChild((w2.monthNav = d("div", "flatpickr-months"), w2.yearElements = [], w2.monthElements = [], w2.prevMonthNav = d("span", "flatpickr-prev-month"), w2.prevMonthNav.innerHTML = w2.config.prevArrow, w2.nextMonthNav = d("span", "flatpickr-next-month"), w2.nextMonthNav.innerHTML = w2.config.nextArrow, V(), Object.defineProperty(w2, "_hidePrevMonthArrow", { get: function() {
                return w2.__hidePrevMonthArrow;
              }, set: function(e3) {
                w2.__hidePrevMonthArrow !== e3 && (s(w2.prevMonthNav, "flatpickr-disabled", e3), w2.__hidePrevMonthArrow = e3);
              } }), Object.defineProperty(w2, "_hideNextMonthArrow", { get: function() {
                return w2.__hideNextMonthArrow;
              }, set: function(e3) {
                w2.__hideNextMonthArrow !== e3 && (s(w2.nextMonthNav, "flatpickr-disabled", e3), w2.__hideNextMonthArrow = e3);
              } }), w2.currentYearElement = w2.yearElements[0], Ce(), w2.monthNav)), w2.innerContainer = d("div", "flatpickr-innerContainer"), w2.config.weekNumbers) {
                var n3 = function() {
                  w2.calendarContainer.classList.add("hasWeeks");
                  var e3 = d("div", "flatpickr-weekwrapper");
                  e3.appendChild(d("span", "flatpickr-weekday", w2.l10n.weekAbbreviation));
                  var n4 = d("div", "flatpickr-weeks");
                  return e3.appendChild(n4), { weekWrapper: e3, weekNumbers: n4 };
                }(), t2 = n3.weekWrapper, a2 = n3.weekNumbers;
                w2.innerContainer.appendChild(t2), w2.weekNumbers = a2, w2.weekWrapper = t2;
              }
              w2.rContainer = d("div", "flatpickr-rContainer"), w2.rContainer.appendChild(z()), w2.daysContainer || (w2.daysContainer = d("div", "flatpickr-days"), w2.daysContainer.tabIndex = -1), U(), w2.rContainer.appendChild(w2.daysContainer), w2.innerContainer.appendChild(w2.rContainer), e2.appendChild(w2.innerContainer);
            }
            w2.config.enableTime && e2.appendChild(function() {
              w2.calendarContainer.classList.add("hasTime"), w2.config.noCalendar && w2.calendarContainer.classList.add("noCalendar");
              var e3 = E(w2.config);
              w2.timeContainer = d("div", "flatpickr-time"), w2.timeContainer.tabIndex = -1;
              var n4 = d("span", "flatpickr-time-separator", ":"), t3 = m("flatpickr-hour", { "aria-label": w2.l10n.hourAriaLabel });
              w2.hourElement = t3.getElementsByTagName("input")[0];
              var a3 = m("flatpickr-minute", { "aria-label": w2.l10n.minuteAriaLabel });
              w2.minuteElement = a3.getElementsByTagName("input")[0], w2.hourElement.tabIndex = w2.minuteElement.tabIndex = -1, w2.hourElement.value = o(w2.latestSelectedDateObj ? w2.latestSelectedDateObj.getHours() : w2.config.time_24hr ? e3.hours : function(e4) {
                switch (e4 % 24) {
                  case 0:
                  case 12:
                    return 12;
                  default:
                    return e4 % 12;
                }
              }(e3.hours)), w2.minuteElement.value = o(w2.latestSelectedDateObj ? w2.latestSelectedDateObj.getMinutes() : e3.minutes), w2.hourElement.setAttribute("step", w2.config.hourIncrement.toString()), w2.minuteElement.setAttribute("step", w2.config.minuteIncrement.toString()), w2.hourElement.setAttribute("min", w2.config.time_24hr ? "0" : "1"), w2.hourElement.setAttribute("max", w2.config.time_24hr ? "23" : "12"), w2.hourElement.setAttribute("maxlength", "2"), w2.minuteElement.setAttribute("min", "0"), w2.minuteElement.setAttribute("max", "59"), w2.minuteElement.setAttribute("maxlength", "2"), w2.timeContainer.appendChild(t3), w2.timeContainer.appendChild(n4), w2.timeContainer.appendChild(a3), w2.config.time_24hr && w2.timeContainer.classList.add("time24hr");
              if (w2.config.enableSeconds) {
                w2.timeContainer.classList.add("hasSeconds");
                var i3 = m("flatpickr-second");
                w2.secondElement = i3.getElementsByTagName("input")[0], w2.secondElement.value = o(w2.latestSelectedDateObj ? w2.latestSelectedDateObj.getSeconds() : e3.seconds), w2.secondElement.setAttribute("step", w2.minuteElement.getAttribute("step")), w2.secondElement.setAttribute("min", "0"), w2.secondElement.setAttribute("max", "59"), w2.secondElement.setAttribute("maxlength", "2"), w2.timeContainer.appendChild(d("span", "flatpickr-time-separator", ":")), w2.timeContainer.appendChild(i3);
              }
              w2.config.time_24hr || (w2.amPM = d("span", "flatpickr-am-pm", w2.l10n.amPM[r((w2.latestSelectedDateObj ? w2.hourElement.value : w2.config.defaultHour) > 11)]), w2.amPM.title = w2.l10n.toggleTitle, w2.amPM.tabIndex = -1, w2.timeContainer.appendChild(w2.amPM));
              return w2.timeContainer;
            }());
            s(w2.calendarContainer, "rangeMode", "range" === w2.config.mode), s(w2.calendarContainer, "animate", true === w2.config.animate), s(w2.calendarContainer, "multiMonth", w2.config.showMonths > 1), w2.calendarContainer.appendChild(e2);
            var i2 = void 0 !== w2.config.appendTo && void 0 !== w2.config.appendTo.nodeType;
            if ((w2.config.inline || w2.config.static) && (w2.calendarContainer.classList.add(w2.config.inline ? "inline" : "static"), w2.config.inline && (!i2 && w2.element.parentNode ? w2.element.parentNode.insertBefore(w2.calendarContainer, w2._input.nextSibling) : void 0 !== w2.config.appendTo && w2.config.appendTo.appendChild(w2.calendarContainer)), w2.config.static)) {
              var l2 = d("div", "flatpickr-wrapper");
              w2.element.parentNode && w2.element.parentNode.insertBefore(l2, w2.element), l2.appendChild(w2.element), w2.altInput && l2.appendChild(w2.altInput), l2.appendChild(w2.calendarContainer);
            }
            w2.config.static || w2.config.inline || (void 0 !== w2.config.appendTo ? w2.config.appendTo : window.document.body).appendChild(w2.calendarContainer);
          }(), function() {
            w2.config.wrap && ["open", "close", "toggle", "clear"].forEach(function(e3) {
              Array.prototype.forEach.call(w2.element.querySelectorAll("[data-" + e3 + "]"), function(n4) {
                return P(n4, "click", w2[e3]);
              });
            });
            if (w2.isMobile) return void function() {
              var e3 = w2.config.enableTime ? w2.config.noCalendar ? "time" : "datetime-local" : "date";
              w2.mobileInput = d("input", w2.input.className + " flatpickr-mobile"), w2.mobileInput.tabIndex = 1, w2.mobileInput.type = e3, w2.mobileInput.disabled = w2.input.disabled, w2.mobileInput.required = w2.input.required, w2.mobileInput.placeholder = w2.input.placeholder, w2.mobileFormatStr = "datetime-local" === e3 ? "Y-m-d\\TH:i:S" : "date" === e3 ? "Y-m-d" : "H:i:S", w2.selectedDates.length > 0 && (w2.mobileInput.defaultValue = w2.mobileInput.value = w2.formatDate(w2.selectedDates[0], w2.mobileFormatStr));
              w2.config.minDate && (w2.mobileInput.min = w2.formatDate(w2.config.minDate, "Y-m-d"));
              w2.config.maxDate && (w2.mobileInput.max = w2.formatDate(w2.config.maxDate, "Y-m-d"));
              w2.input.getAttribute("step") && (w2.mobileInput.step = String(w2.input.getAttribute("step")));
              w2.input.type = "hidden", void 0 !== w2.altInput && (w2.altInput.type = "hidden");
              try {
                w2.input.parentNode && w2.input.parentNode.insertBefore(w2.mobileInput, w2.input.nextSibling);
              } catch (e4) {
              }
              P(w2.mobileInput, "change", function(e4) {
                w2.setDate(g(e4).value, false, w2.mobileFormatStr), De("onChange"), De("onClose");
              });
            }();
            var e2 = l(re, 50);
            w2._debouncedChange = l(Y, 300), w2.daysContainer && !/iPhone|iPad|iPod/i.test(navigator.userAgent) && P(w2.daysContainer, "mouseover", function(e3) {
              "range" === w2.config.mode && oe(g(e3));
            });
            P(w2._input, "keydown", ie), void 0 !== w2.calendarContainer && P(w2.calendarContainer, "keydown", ie);
            w2.config.inline || w2.config.static || P(window, "resize", e2);
            void 0 !== window.ontouchstart ? P(window.document, "touchstart", X) : P(window.document, "mousedown", X);
            P(window.document, "focus", X, { capture: true }), true === w2.config.clickOpens && (P(w2._input, "focus", w2.open), P(w2._input, "click", w2.open));
            void 0 !== w2.daysContainer && (P(w2.monthNav, "click", xe), P(w2.monthNav, ["keyup", "increment"], N), P(w2.daysContainer, "click", me));
            if (void 0 !== w2.timeContainer && void 0 !== w2.minuteElement && void 0 !== w2.hourElement) {
              var n3 = function(e3) {
                return g(e3).select();
              };
              P(w2.timeContainer, ["increment"], _), P(w2.timeContainer, "blur", _, { capture: true }), P(w2.timeContainer, "click", H), P([w2.hourElement, w2.minuteElement], ["focus", "click"], n3), void 0 !== w2.secondElement && P(w2.secondElement, "focus", function() {
                return w2.secondElement && w2.secondElement.select();
              }), void 0 !== w2.amPM && P(w2.amPM, "click", function(e3) {
                _(e3);
              });
            }
            w2.config.allowInput && P(w2._input, "blur", ae);
          }(), (w2.selectedDates.length || w2.config.noCalendar) && (w2.config.enableTime && F(w2.config.noCalendar ? w2.latestSelectedDateObj : void 0), ye(false)), S();
          var n2 = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
          !w2.isMobile && n2 && de(), De("onReady");
        }(), w2;
      }
      function T(e2, n2) {
        for (var t2 = Array.prototype.slice.call(e2).filter(function(e3) {
          return e3 instanceof HTMLElement;
        }), a2 = [], i2 = 0; i2 < t2.length; i2++) {
          var o2 = t2[i2];
          try {
            if (null !== o2.getAttribute("data-fp-omit")) continue;
            void 0 !== o2._flatpickr && (o2._flatpickr.destroy(), o2._flatpickr = void 0), o2._flatpickr = k(o2, n2 || {}), a2.push(o2._flatpickr);
          } catch (e3) {
            console.error(e3);
          }
        }
        return 1 === a2.length ? a2[0] : a2;
      }
      "undefined" != typeof HTMLElement && "undefined" != typeof HTMLCollection && "undefined" != typeof NodeList && (HTMLCollection.prototype.flatpickr = NodeList.prototype.flatpickr = function(e2) {
        return T(this, e2);
      }, HTMLElement.prototype.flatpickr = function(e2) {
        return T([this], e2);
      });
      var I = function(e2, n2) {
        return "string" == typeof e2 ? T(window.document.querySelectorAll(e2), n2) : e2 instanceof Node ? T([e2], n2) : T(e2, n2);
      };
      return I.defaultConfig = {}, I.l10ns = { en: e({}, i), default: e({}, i) }, I.localize = function(n2) {
        I.l10ns.default = e(e({}, I.l10ns.default), n2);
      }, I.setDefaults = function(n2) {
        I.defaultConfig = e(e({}, I.defaultConfig), n2);
      }, I.parseDate = C({}), I.formatDate = b({}), I.compareDates = M, "undefined" != typeof jQuery && void 0 !== jQuery.fn && (jQuery.fn.flatpickr = function(e2) {
        return T(this, e2);
      }), Date.prototype.fp_incr = function(e2) {
        return new Date(this.getFullYear(), this.getMonth(), this.getDate() + ("string" == typeof e2 ? parseInt(e2, 10) : e2));
      }, "undefined" != typeof window && (window.flatpickr = I), I;
    });
  }
});
export default require_flatpickr();
/* flatpickr v4.6.13,, @license MIT */
//# sourceMappingURL=/assets/flatpickr.js.map
