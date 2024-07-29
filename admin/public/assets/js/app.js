!(function($) {
    "use strict";
    var e,
        t = localStorage.getItem("language"),
        n = "en";
    async function setTheme(e) {
        let getUrl = window.location.origin + "/";
        if (e.length && "dark-mode-switch" === e) {
            await $.get(`${getUrl}admin/set-theme`, {
                theme: "dark"
            });
        } else if (e.length && "light-mode-switch" === e) {
            await $.get(`${getUrl}admin/set-theme`, {
                theme: "light"
            });
        }
        1 == $("#light-mode-switch").prop("checked") &&
            "light-mode-switch" === e ?
            ($("html").removeAttr("dir"),
                // s("#dark-mode-switch").prop("checked", !1),
                // s("#rtl-mode-switch").prop("checked", !1),
                // s("#dark-rtl-mode-switch").prop("checked", !1),
                // s("#bootstrap-style").attr("href", getUrl + "assets/css/bootstrap.min.css"),
                // s("#app-style").attr("href", getUrl + "assets/css/app.min.css"),
                // s("#custom-style").attr("href", getUrl + "assets/css/custom-light.css"),
                sessionStorage.setItem("is_visited", "light-mode-switch")) :
            1 == $("#dark-mode-switch").prop("checked") &&
            "dark-mode-switch" === e ?
            ($("html").removeAttr("dir"),
                // s("#light-mode-switch").prop("checked", !1),
                // s("#rtl-mode-switch").prop("checked", !1),
                // s("#dark-rtl-mode-switch").prop("checked", !1),
                // s("#bootstrap-style").attr("href", getUrl + "assets/css/bootstrap-dark.min.css"),
                // s("#app-style").attr("href", getUrl + "assets/css/app-dark.min.css"),
                // s("#custom-style").attr("href", getUrl + "assets/css/custom-dark.css"),
                sessionStorage.setItem("is_visited", "dark-mode-switch")) :
            1 == $("#rtl-mode-switch").prop("checked") &&
            "rtl-mode-switch" === e ?
            ($("#light-mode-switch").prop("checked", !1),
                // s("#dark-mode-switch").prop("checked", !1),
                // s("#dark-rtl-mode-switch").prop("checked", !1),
                // s("#bootstrap-style").attr("href", "assets/css/bootstrap-rtl.min.css"),
                // s("#app-style").attr("href", "assets/css/app-rtl.min.css"),
                // s("html").attr("dir", "rtl"),
                sessionStorage.setItem("is_visited", "rtl-mode-switch")) :
            1 == $("#dark-rtl-mode-switch").prop("checked") &&
            "dark-rtl-mode-switch" === e &&
            ($("#light-mode-switch").prop("checked", !1),
                // s("#rtl-mode-switch").prop("checked", !1),
                // s("#dark-mode-switch").prop("checked", !1),
                // s("#bootstrap-style").attr("href", "assets/css/bootstrap-dark-rtl.min.css"),
                // s("#app-style").attr("href", "assets/css/app-dark-rtl.min.css"),
                // s("html").attr("dir", "rtl"),
                sessionStorage.setItem("is_visited", "dark-rtl-mode-switch"));
    }

    function exitFullscreen() {
        document.webkitIsFullScreen ||
            document.mozFullScreen ||
            document.msFullscreenElement ||
            (console.log("pressed"),
                $("body").removeClass("fullscreen-enable"));
    }
    $("#side-menu").metisMenu(),
    $("#vertical-menu-btn").on("click", function(e) {
        e.preventDefault(),
            $("body").toggleClass("sidebar-enable"),
            992 <= $(window).width() ?
            $("body").toggleClass("vertical-collpsed") :
            $("body").removeClass("vertical-collpsed");
    }),
    $("#sidebar-menu a").each(function() {
        var e = window.location.href.split(/[?#]/)[0];
        this.href == e &&
            ($(this).addClass("active"),
                $(this).parent().addClass("mm-active"),
                $(this).parent().parent().addClass("mm-show"),
                $(this).parent().parent().prev().addClass("mm-active"),
                $(this).parent().parent().parent().addClass("mm-active"),
                $(this).parent().parent().parent().parent().addClass("mm-show"),
                $(this)
                .parent()
                .parent()
                .parent()
                .parent()
                .parent()
                .addClass("mm-active"));
    }),
    $(document).ready(function() {
        var e;
        0 < $("#sidebar-menu").length &&
            0 < $("#sidebar-menu .mm-active .active").length &&
            300 <
            (e = $("#sidebar-menu .mm-active .active").offset().top) &&
            ((e -= 300),
                $(".vertical-menu .simplebar-content-wrapper").animate({
                        scrollTop: e
                    },
                    "slow"
                ));
    }),
    $(".navbar-nav a").each(function() {
        var e = window.location.href.split(/[?#]/)[0];
        this.href == e &&
            ($(this).addClass("active"),
                $(this).parent().addClass("active"),
                $(this).parent().parent().addClass("active"),
                $(this).parent().parent().parent().addClass("active"),
                $(this).parent().parent().parent().parent().addClass("active"),
                $(this)
                .parent()
                .parent()
                .parent()
                .parent()
                .parent()
                .addClass("active"),
                $(this)
                .parent()
                .parent()
                .parent()
                .parent()
                .parent()
                .parent()
                .addClass("active"));
    }),
    $('[data-bs-toggle="fullscreen"]').on("click", function(e) {
        e.preventDefault(),
            $("body").toggleClass("fullscreen-enable"),
            document.fullscreenElement ||
            document.mozFullScreenElement ||
            document.webkitFullscreenElement ?
            document.cancelFullScreen ?
            document.cancelFullScreen() :
            document.mozCancelFullScreen ?
            document.mozCancelFullScreen() :
            document.webkitCancelFullScreen &&
            document.webkitCancelFullScreen() :
            document.documentElement.requestFullscreen ?
            document.documentElement.requestFullscreen() :
            document.documentElement.mozRequestFullScreen ?
            document.documentElement.mozRequestFullScreen() :
            document.documentElement.webkitRequestFullscreen &&
            document.documentElement.webkitRequestFullscreen(
                Element.ALLOW_KEYBOARD_INPUT
            );
    }),
    document.addEventListener("fullscreenchange", exitFullscreen),
    document.addEventListener("webkitfullscreenchange", exitFullscreen),
    document.addEventListener("mozfullscreenchange", exitFullscreen),
    $(".right-bar-toggle").on("click", function(e) {
        $("body").toggleClass("right-bar-enabled");
    }),
    $(document).on("click", "body", function(e) {
        0 < $(e.target).closest(".right-bar-toggle, .right-bar").length ||
            $("body").removeClass("right-bar-enabled");
    }),
    (function() {
        if (document.getElementById("topnav-menu-content")) {
            for (
                var e = document
                    .getElementById("topnav-menu-content")
                    .getElementsByTagName("a"),
                    t = 0,
                    s = e.length; t < s; t++
            )
                e[t].onclick = function(e) {
                    "#" === e.target.getAttribute("href") &&
                        (e.target.parentElement.classList.toggle("active"),
                            e.target.nextElementSibling.classList.toggle(
                                "show"
                            ));
                };
            window.addEventListener("resize", c);
        }
    })(),
    [].slice
    .call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    .map(function(e) {
        return new bootstrap.Tooltip(e);
    }),
    [].slice
    .call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    .map(function(e) {
        return new bootstrap.Popover(e);
    }),
    [].slice
    .call(document.querySelectorAll(".offcanvas"))
    .map(function(e) {
        return new bootstrap.Offcanvas(e);
    }),
    window.sessionStorage &&
    ((e = sessionStorage.getItem("is_visited")) ?
        ($(".right-bar input:checkbox").prop("checked", !1),
            $("#" + e).prop("checked", !0),
            setTheme(e)) :
        sessionStorage.setItem("is_visited", "light-mode-switch")),

    $("#light-mode-switch, #dark-mode-switch, #rtl-mode-switch, #dark-rtl-mode-switch").on("change", async function(e) {
        await setTheme(e.target.id);
        location.reload();
    }),
    $("#password-addon").on("click", function() {
        0 < $(this).siblings("input").length &&
            ("password" == $(this).siblings("input").attr("type") ?
                $(this).siblings("input").attr("type", "input") :
                $(this).siblings("input").attr("type", "password"));
    }),

    $("#checkAll").on("change", function() {
        $(".table-check .form-check-input").prop(
            "checked",
            $(this).prop("checked")
        );
    }),
    $(".table-check .form-check-input").change(function() {
        $(".table-check .form-check-input:checked").length ==
            $(".table-check .form-check-input").length ?
            $("#checkAll").prop("checked", !0) :
            $("#checkAll").prop("checked", !1);
    });
})(jQuery);
