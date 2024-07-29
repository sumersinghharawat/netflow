var nextPage = 2;

$(() => {
    $(".summernote").summernote({
        styleWithSpan: false,
        toolbar: [
            ["style", ["style", "bold", "italic", "underline", "clear"]],
            ["font", ["strikethrough", "superscript", "subscript"]],
            ["fontsize", ["fontsize"]],
            ["fontname", ["fontname"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["height", ["height"]],
            ["table", ["table"]],
            ["insert", ["link", "image", "elfinder", "hr"]],
            ["view", ["codeview"]],
            ["help", ["help"]],
        ],
    });

    let buttons = $('.note-editor button[data-toggle="dropdown"]');

    buttons.each((key, value) => {
        $(value).on("click", function (e) {
            $(this).attr("data-bs-toggle", "dropdown");
        });
    });

    $(".select2").select2({
        width: "100%",
        placeholder: "Choose...",
        allowClear: true,
    });
    setTimeout(() => {
        $(".demo-note").slideUp("slow", function () {
            $("#demo-note").remove();
        });
    }, 10000);

    toastr.options = {
        closeButton: true,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: true,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };
    $(".outer-button").click(function () {
        console.log("clicked");
        $(".inner-menu").toggleClass("closed");
    });

    const notificationsContainer = document.querySelector(
        "#notification-container"
    );
    let isLoading = false;
    notificationsContainer.addEventListener("scroll", () => {
        if (isLoading || nextPage === null) {
            return;
        }
        const containerHeight = notificationsContainer.clientHeight;
        const contentHeight = notificationsContainer.scrollHeight;
        const scrollTop = notificationsContainer.scrollTop;
        if (scrollTop + 1 >= contentHeight - containerHeight) {
            loadMoreNotifications();
        }
    });
});
const serverAlert = (error, code) => {
    return `<div class="server-alert">
                <button class="close" type="button" id="demo-note-close"><i class="icon-close"></i></button>
                <div class="demo-note-container">
                    <div class="info-icon-server-alert">
                        <div><i class="fa fa-info" aria-hidden="true"></i></div>
                    </div>
                    <div class="demo-note-cnt server-alert-ctn">
                        <p class="">Server Not Responding</p><br>
                        <small class=''>${error}</small>
                        <div class="close-icon">
                            <div><i class="fa fa-times" aria-hidden="true"></i></div>
                        </div>
                    </div>
                </div>
            </div>`;
};

const closeDemonote = () => {
    $(".demo-note").slideUp("slow", function () {
        $("#demo-note").remove();
    });
};

const initTooltip = () => {
    $(".tooltipKey").serialtip({
        event: "hover",
    });
};

const withCurrency = (value) => {
    return "$ " + value;
};

const getUsers = () => {
    $(".select2-search-user").select2({
        placeholder: "Username",
        width: "100%",
        minimumInputLength: 1,
        allowClear: true,
        ajax: {
            url: "/admin/all-users",
            dataType: "json",
            delay: 10,
            infiniteScroll: true,
            data: function (params) {
                var query = {
                    search: params.term == "/" ? null : params.term,
                    page: params.page || 1,
                };

                return query;
            },
            processResults: function (result) {
                return {
                    results: $.map(result.data.data, function (item) {
                        return {
                            text: item.username,
                            id: item.id,
                        };
                    }),
                    pagination: {
                        more: result.data.current_page * 10 < result.data.total,
                    },
                };
            },
            cache: true,
        },
    });
    // $(".select2-search__field").attr("placeholder", "Enter your search here");
};

const getEmployees = () => {
    $(".select2-search-employee").select2({
        placeholder: "Username",
        width: "100%",
        ajax: {
            url: "/admin/get/employees",
            dataType: "json",
            delay: 10,
            processResults: function (result) {
                return {
                    results: $.map(result.data, function (item) {
                        return {
                            text: item.username,
                            id: item.id,
                        };
                    }),
                };
            },
            cache: true,
        },
    });
};

const getTickets = () => {
    $(".select2-search-ticket").select2({
        placeholder: "Ticket Id",
        width: "100%",
        ajax: {
            url: "/admin/get/ticket/ids",
            dataType: "json",
            delay: 10,
            processResults: function (result) {
                return {
                    results: $.map(result.data, function (item) {
                        return {
                            text: item.track_id,
                            id: item.id,
                        };
                    }),
                };
            },
            cache: true,
        },
    });
};
const getEpin = () => {
    $(".select2-search-epin").select2({
        placeholder: "Epin",
        width: "100%",
        ajax: {
            url: "/admin/get/epin",
            dataType: "json",
            delay: 10,
            processResults: function (result) {
                return {
                    results: $.map(result.data, function (item) {
                        return {
                            text: item.numbers,
                            id: item.id,
                        };
                    }),
                };
            },
            cache: true,
        },
    });
};

const getAmounts = () => {
    $(".select2-search-amount").select2({
        placeholder: "Amount",
        width: "100%",
        ajax: {
            url: "get/pin/amounts",
            dataType: "json",
            delay: 10,
            processResults: function (result) {
                return {
                    results: $.map(result.data, function (item) {
                        return {
                            text: item.amount,
                            id: item.amount,
                        };
                    }),
                };
            },
            cache: true,
        },
    });
};
const getEmployeesInsideCanvas = (canvasId, selectId) => {
    $(`#${selectId}`).select2({
        placeholder: "Username",
        dropdownParent: $(`#${canvasId}`),
        width: "100%",
        ajax: {
            url: "/admin/get/employees",
            dataType: "json",
            delay: 10,
            processResults: function (result) {
                return {
                    results: $.map(result.data, function (item) {
                        return {
                            text: item.username,
                            id: item.id,
                        };
                    }),
                };
            },
            cache: true,
        },
    });
};

const getUsersInsideCanvas = (canvasId, selectId) => {
    $(`#${selectId}`).select2({
        placeholder: "Username",
        dropdownParent: $(`#${canvasId}`),
        width: "100%",
        minimumInputLength: 1,
        allowClear: true,
        ajax: {
            url: "/admin/all-users",
            dataType: "json",
            delay: 10,
            infiniteScroll: true,
            data: function (params) {
                var query = {
                    search: params.term == "/" ? null : params.term,
                    page: params.page || 1,
                };

                return query;
            },
            processResults: function (result) {
                return {
                    results: $.map(result.data.data, function (item) {
                        return {
                            text: item.username,
                            id: item.id,
                        };
                    }),
                    pagination: {
                        more: result.data.current_page * 10 < result.data.total,
                    },
                };
            },
            cache: true,
        },
    });
};
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    error: (err, status, res) => {
        if (err.status === 500) {
            $("#server-alert-parent-div").html(
                serverAlert(err.responseJSON.message, err.status)
            );
            $("#server-alert-parent-div").show("slow", () => {
                setTimeout(() => {
                    $("#server-alert-parent-div").hide("slow");
                }, 3000);
            });
        } else if (err.status === 404) {
            $("#server-alert-parent-div").html(
                serverAlert(err.responseJSON.message, err.status)
            );
            $("#server-alert-parent-div").show("slow", () => {
                setTimeout(() => {
                    $("#server-alert-parent-div").hide("slow");
                }, 3000);
            });
        } else if (err.status === 400) {
            $("#server-alert-parent-div").html(
                serverAlert(err.responseJSON.message, err.status)
            );
            $("#server-alert-parent-div").show("slow", () => {
                setTimeout(() => {
                    $("#server-alert-parent-div").hide("slow");
                }, 3000);
            });
        }
    },
});

const notifySuccess = (message) => {
    toastr.success(message);
};

const notifyError = async (message) => {
    toastr.error(message);
};

const confirmSwal = () => {
    return Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, delete it!",
    });
};
const successAlert = () => {
    Swal.fire("Deleted!", "Your file has been deleted.", "success");
};

const getForm = (form) => {
    let formdata = new FormData(form);
    let obj = {};
    for (let [key, value] of formdata) {
        obj[key] = value;
    }
    return obj;
};

const formvalidationError = (form, err) => {
    form.classList.remove("was-validated");
    let errors = err.responseJSON.errors;
    $(".invalid-feedback").remove();
    Object.keys(errors).map((error, key) => {
        let msg = errors[error][0];
        if (error.includes(".")) {
            let multiErr = error.split(".");
            let inputName = `${multiErr[0]}[${multiErr[1]}]`;
            let errElement = $(`input[name="${inputName}"]`);
            let errorMessage = `<div class="invalid-feedback d-block">${msg}</div>`;
            errElement.addClass("is-invalid d-block");
            errElement.after(errorMessage);
        } else {
            let errElement = $(`input[name="${error}"]`);
            let errElementSelect = $(`select[name="${error}"]`);

            let errorMessage = `<div class="invalid-feedback d-block">${msg}</div>`;
            errElement.addClass("is-invalid d-block");
            errElement.after(errorMessage);
            errElementSelect.after(errorMessage);
        }
    });
};

const inputvalidationError = (id, err) => {
    let errElement = $(`input[id="${id}"]`);
    errElement.removeClass("was-validated");
    let errors = err.responseJSON.errors;
    Object.keys(errors).map((error, key) => {
        let msg = errors[error][0];
        let errorMessage = `<div class="invalid-feedback d-block">${msg}</div>`;
        errElement.addClass("is-invalid d-block");
        errElement.after(errorMessage);
    });
};
const selectvalidationError = (id, err) => {
    let errElement = $(`select[id="${id}"]`);
    errElement.removeClass("was-validated");
    let errors = err.responseJSON.errors;
    Object.keys(errors).map((error, key) => {
        let msg = errors[error][0];
        let errorMessage = `<div class="invalid-feedback d-block">${msg}</div>`;
        console.log(errorMessage);
        errElement.addClass("is-invalid d-block");
        errElement.after(errorMessage);
    });
};

const elementvalidationError = (id, err, errAfter) => {
    let errElement = $(`input[id="${id}"]`);
    errElement.removeClass("was-validated");
    let errors = err.responseJSON.errors;
    Object.keys(errors).map((error, key) => {
        let msg = errors[error][0];
        let errorMessage = `<div class="invalid-feedback d-block">${msg}</div>`;
        errElement.addClass("is-invalid d-block");
        $(`#${errAfter}`).after(errorMessage);
    });
};

const confirmActivate = () => {
    return Swal.fire({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes!",
    });
};

const confirmDefault = () => {
    return Swal.fire({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Make this as my default address!",
    });
};
const confirmLanguage = async (lang) => {
    let url = "/admin/user-language";
    let data = {
        language: lang,
        _method: "patch",
    };
    const res = await $.post(`${url}`, data)
    .catch((err) => {
        console.log(err);
        if (err.status === 422) {
            let errors = err.responseJSON.errors;
            notifyError(errors);
        } else if (err.status === 401) {
            let errors = err.responseJSON.errors;
            notifyError(errors);
        }
    });
    console.log(res);
    notifySuccess(res.message)
    window.location.reload();
};

const changeCurrency = async (currency) => {
    console.log("here");
    const res = await $.get("/admin/currency-change", { currency })
        .then((res) => {
            window.location.reload();
        })
        .catch((err) => {
            if (err.status === 422) {
                let msg = err.responseJSON.message;
                notifyError(msg);
                console.log(msg);
            }
        });

    // notifySuccess(res.message);
};

const confirmApprove = () => {
    return Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Approve",
    });
};

const confirmReject = () => {
    return Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Reject",
    });
};

const validationErrorsById = (err) => {
    let errors = err.responseJSON.errors;
    $(".invalid-feedback").remove();
    for (let [key, value] of Object.entries(errors)) {
        $(`#${key}`).addClass("is-invalid");
        let errorMessage = `<div class="invalid-feedback d-block">${value[0]}</div>`;
        if (key == "terms") {
            $("#terms_condition").after(errorMessage);
        } else {
            $(`#${key}`).after(errorMessage);
        }
    }
};

const copyClipBoard = (id) => {
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
};

function getCookies(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(";");
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == " ") {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return false;
}

const printDiv = (id) => {
    try {
        var divToPrint = document.getElementById(id);
        newWin = window.open("");
        newWin.document.write(divToPrint.outerHTML);
        newWin.print();
        newWin.close();
    } catch (error) {
        console.log(error);
    }
};

const readNotification = async (id) => {
    const res = await $.get("/read-notification", { id }).catch((err) => {
        if (err.status === 422) {
            alert("validation error");
        }
    });
    notifySuccess(res.message);
    window.location.reload();
};

const markAsRead = async () => {
    const res = await $.get("/read-all-notification").catch((err) => {
        if (err.status === 422) {
            alert("validation error");
        }
    });
    notifySuccess(res.message);
    window.location.reload();
};

const loadMoreNotifications = async () => {
    console.log(`next page: ${nextPage}`);
    const res = await $.get("/notifications?page=" + nextPage);
    nextPage = res.next_page_url ? res.current_page + 1 : null;
    $("#notification-container").append(res.html);
};
