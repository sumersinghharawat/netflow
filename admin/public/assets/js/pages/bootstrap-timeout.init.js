!(function (a) {
    "use strict";
    a.sessionTimeout = function (b) {
        function c() {
            n ||
                (a.ajax({
                    type: i.ajaxType,
                    url: i.keepAliveUrl,
                    data: i.ajaxData,
                }),
                (n = !0),
                setTimeout(function () {
                    n = !1;
                }, i.keepAliveInterval));
        }
        function d() {
            clearTimeout(g),
                (i.countdownMessage || i.countdownBar) && f("session", !0),
                "function" == typeof i.onStart && i.onStart(i),
                i.keepAlive && c(),
                (g = setTimeout(function () {
                    "function" != typeof i.onWarn
                        ? a("#session-timeout-dialog").modal("show")
                        : i.onWarn(i),
                        e();
                }, i.warnAfter));
        }
        function e() {
            $.get(i.logoutUrl);
            clearTimeout(g),
                a("#session-timeout-dialog").hasClass("in") ||
                    (!i.countdownMessage && !i.countdownBar) ||
                    f("dialog", !0),
                (g = setTimeout(function () {
                    "function" != typeof i.onRedir
                        ? (window.location = i.redirUrl)
                        : i.onRedir(i);
                }, i.redirAfter - i.warnAfter));
        }
        function f(b, c) {
            clearTimeout(j.timer),
                "dialog" === b && c
                    ? (j.timeLeft = Math.floor(
                          (i.redirAfter - i.warnAfter) / 1e3
                      ))
                    : "session" === b &&
                      c &&
                      (j.timeLeft = Math.floor(i.redirAfter / 1e3)),
                i.countdownBar && "dialog" === b
                    ? (j.percentLeft = Math.floor(
                          (j.timeLeft / ((i.redirAfter - i.warnAfter) / 1e3)) *
                              100
                      ))
                    : i.countdownBar &&
                      "session" === b &&
                      (j.percentLeft = Math.floor(
                          (j.timeLeft / (i.redirAfter / 1e3)) * 100
                      ));
            var d = a(".countdown-holder"),
                e = j.timeLeft >= 0 ? j.timeLeft : 0;
            if (i.countdownSmart) {
                var g = Math.floor(e / 60),
                    h = e % 60,
                    k = g > 0 ? g + "m" : "";
                k.length > 0 && (k += " "), (k += h + "s"), d.text(k);
            } else d.text(e + "s");
            i.countdownBar &&
                a(".countdown-bar").css("width", j.percentLeft + "%"),
                (j.timeLeft = j.timeLeft - 1),
                (j.timer = setTimeout(function () {
                    f(b);
                }, 1e3));
        }
        var g,
            h = {
                title: "Your Session is About to Expire!",
                message: "Your session is about to expire.",
                logoutButton: "Logout",
                keepAliveButton: "Stay Connected",
                // keepAliveUrl: "/keep-alive",
                keepAliveUrl: "/",
                // ajaxType: "POST",
                ajaxData: "",
                // redirUrl: "/timed-out",
                // logoutUrl: "/log-out",
                redirUrl: "/lockscreen",
                logoutUrl: "/auto/logout",
                warnAfter: 9e5,
                redirAfter: 12e5,
                keepAliveInterval: 5e3,
                keepAlive: !0,
                ignoreUserActivity: !1,
                onStart: !1,
                onWarn: !1,
                onRedir: !1,
                countdownMessage: !1,
                countdownBar: !1,
                countdownSmart: !1,
            },
            i = h,
            j = {};
        // if ((b && (i = a.extend(h, b)), i.warnAfter <= i.redirAfter))   //origin
        if ((b && (i = a.extend(h, b)), i.warnAfter >= i.redirAfter))   //testing
            return (
                console.error(
                    'Bootstrap-session-timeout plugin is miss-configured. Option "redirAfter" must be equal or greater than "warnAfter".'
                ),
                !1
            );
        if ("function" != typeof i.onWarn) {
            var k = i.countdownMessage
                    ? "<p>" +
                      i.countdownMessage.replace(
                          /{timer}/g,
                          '<span class="countdown-holder"></span>'
                      ) +
                      "</p>"
                    : "",
                l = i.countdownBar
                    ? '<div class="progress">                   <div class="progress-bar progress-bar-striped countdown-bar active" role="progressbar" style="min-width: 15px; width: 100%;">                     <span class="countdown-holder"></span>                   </div>                 </div>'
                    : "";
            a("body").append(
                '<div class="modal fade" id="session-timeout-dialog">               <div class="modal-dialog">                 <div class="modal-content">                   <div class="modal-header">                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>                     <h4 class="modal-title">' +
                    i.title +
                    '</h4>                   </div>                   <div class="modal-body">                     <p>' +
                    i.message +
                    "</p>                     " +
                    k +
                    "                     " +
                    l +
                    '                   </div>                   <div class="modal-footer">                     <button id="session-timeout-dialog-logout" type="button" class="btn btn-default">' +
                    i.logoutButton +
                    '</button>                     <button id="session-timeout-dialog-keepalive" type="button" class="btn btn-primary" data-dismiss="modal">' +
                    i.keepAliveButton +
                    "</button>                   </div>                 </div>               </div>              </div>"
            ),
                a("#session-timeout-dialog-logout").on("click", function () {
                    window.location = i.logoutUrl;
                    // alert('hlo');
                    // $.get(i.logoutUrl);
                    // window.location = i.redirUrl;
                }),
                a("#session-timeout-dialog").on("hide.bs.modal", function () {
                    d();
                });
        }
        if (!i.ignoreUserActivity) {
            var m = [-1, -1];
            a(document).on(
                "keyup mouseup mousemove touchend touchmove",
                function (b) {
                    if ("mousemove" === b.type) {
                        if (b.clientX === m[0] && b.clientY === m[1]) return;
                        (m[0] = b.clientX), (m[1] = b.clientY);
                    }
                    d(),
                        a("#session-timeout-dialog").length > 0 &&
                            a("#session-timeout-dialog").data("bs.modal") &&
                            a("#session-timeout-dialog").data("bs.modal")
                                .isShown &&
                            (a("#session-timeout-dialog").modal("hide"),
                            a("body").removeClass("modal-open"),
                            a("div.modal-backdrop").remove());
                }
            );
        }
        var n = !1;
        d();
    };
})(jQuery);
