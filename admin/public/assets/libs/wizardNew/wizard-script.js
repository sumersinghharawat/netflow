jQuery(document).ready(async function () {
    // click on next button
    jQuery(".form-wizard-next-btn").click(async function () {
        var parentFieldset = jQuery(this).parents(".wizard-fieldset");
        var currentActiveStep = jQuery(this)
            .parents(".form-wizard")
            .find(".form-wizard-steps .active");
        var next = jQuery(this);
        var nextWizardStep = false;

        if ($(this).attr("id") == "tab-1") {
            let username = $("#sponsor-username").val();
            $(".invalid-feedback").remove();
            $("#sponsor-username")
                .removeClass("is-invalid")
                .removeClass("is-valid");
            let regFromTree = $('input[name=regFromTree]').val();

            let placementUsername = $("input[name='placement_username']").val();
            let url = `/admin/sponsor`;
            url = url + `?sponsor=${username}&placement=${placementUsername}&regfrom_tree=${regFromTree}`;
            const res = await $.get(`${url}`).catch((err) => {
                if (err.status === 422) {
                    nextWizardStep = false;
                    elementvalidationError(
                        "sponsor-username",
                        err,
                        "sponsor-username"
                    );
                }
            });
            if (typeof res != "undefined") {
                $("#sponsor-full-name").val(
                    res.data.user_detail.name +
                    " " +
                    res.data.user_detail.second_name
                );
                $("#sponsorId").val(res.data.id);
                $("#sponsor-username").addClass("is-valid");
                nextWizardStep = true;
            }
        }

        if ($(this).attr("id") == "tab-2") {

            let dateOfBirth = $("#dateOfBirth").val();
            let email = $("#email").val();
            let mobile = $("#mobile").val();
            $("#mobile").removeClass("is-invalid");
            $("#email").removeClass("is-invalid");
            $("#error_mobile").html("");
            $("#error_email").html("");

            let wizardDobStatus = false;
            let wizardMobStatus = false;

            if (dateOfBirth != null) {
                $(".invalid-feedback").remove();
                let url = `/admin/check-dob?dob=${dateOfBirth}`;
                const res = await $.get(`${url}`).catch((err) => {
                    if (err.status == 422) {
                        nextWizardStep = false;
                        elementvalidationError(
                            "dateOfBirth",
                            err,
                            "datepicker2"
                        );
                    }
                });
                if (typeof res != "undefined") {
                    wizardDobStatus = true;
                }
            }
            let data = {
                mobile: mobile,
                email: email,
            };
            let u = `/admin/check-mobile`;
            const valid = await $.post(`${u}`, data).catch((err) => {
                if (err.status == 422) {
                    nextWizardStep = false;
                    if(err.responseJSON.mobile){
                        $("#mobile").addClass("is-invalid");
                        $("#error_mobile").html("");
                        $("#error_mobile").html(err.responseJSON.mobile[0]);
                    }
                    if(err.responseJSON.email){
                        $("#error_email").html(err.responseJSON.email[0]);
                        $("#email").addClass("is-invalid");
                    }
                    if (err.responseJSON.email) {
                        $("#error_email").html(err.responseJSON.email);
                        $("#email").addClass("is-invalid");
                    }
                    return;
                }
            });
            if (typeof valid != "undefined") {
                wizardMobStatus = true;
            }
            nextWizardStep = (wizardMobStatus && wizardDobStatus);
        }

        if ($(this).attr("id") == "tab-3") {
            $("#username").removeClass("is-valid");
            $("#username").removeClass("is-invalid");
            $("#password").removeClass("is-valid");
            $("#password").removeClass("is-invalid");
            $("#confirm").removeClass("is-invalid");
            $("#terms").removeClass("is-invalid");
            let username = $("#username").val();
            let password = $("#password").val();
            let confPass = $("#confirm").val();
            let terms = $("#terms").is(":checked") ? "yes" : "no";
            let url = `/admin/check-username`;
            url =
                url +
                `?username=${username}&password=${password}&confirm=${confPass}&terms=${terms}`;

            const res = await $.get(`${url}`).catch((err) => {
                if (err.status == 422) {
                    nextWizardStep = false;
                    validationErrorsById(err);
                }
            });
            if (typeof res != "undefined") {

                $("#username").addClass("is-valid");
                $("#password").addClass("is-valid");
                nextWizardStep = true;
            }
        }
        parentFieldset.find(".wizard-required").each(function () {
            var thisValue = jQuery(this).val();
            console.log(thisValue);
            let dateOfBirthReplica = $("#dateOfBirthReplica").val();
            let dateOfBirth = $("#dateOfBirth").val();
            let ageLimit = $("#ageLimit").val();
            if (thisValue == "") {
                jQuery(this).siblings(".wizard-form-error").slideDown();
                jQuery(this).addClass("fishy");
                // jQuery(this).siblings(".error").html("validation.this_field_is_required");
                nextWizardStep = false;
            } else {
                jQuery(this).siblings(".wizard-form-error").slideUp();
                // jQuery(this).siblings(".error").html("");
                jQuery(this).removeClass("fishy");

                if (nextWizardStep) {
                    nextWizardStep = true;
                } else {
                    nextWizardStep = false;
                }
            }
        });
        nextTab(nextWizardStep, currentActiveStep, next);
    });
    //click on previous button
    jQuery(".form-wizard-previous-btn").click(function () {
        var counter = parseInt(jQuery(".wizard-counter").text());
        var prev = jQuery(this);
        var currentActiveStep = jQuery(this)
            .parents(".form-wizard")
            .find(".form-wizard-steps .active");
        prev.parents(".wizard-fieldset").removeClass("show", "400");
        prev.parents(".wizard-fieldset")
            .prev(".wizard-fieldset")
            .addClass("show", "400");
        currentActiveStep
            .removeClass("active")
            .prev()
            .removeClass("activated")
            .addClass("active", "400");
        jQuery(document)
            .find(".wizard-fieldset")
            .each(function () {
                if (jQuery(this).hasClass("show")) {
                    var formAtrr = jQuery(this).attr("data-tab-content");
                    jQuery(document)
                        .find(".form-wizard-steps .form-wizard-step-item")
                        .each(function () {
                            if (jQuery(this).attr("data-attr") == formAtrr) {
                                jQuery(this).addClass("active");
                                var innerWidth = jQuery(this).innerWidth();
                                var position = jQuery(this).position();
                                jQuery(document)
                                    .find(".form-wizard-step-move")
                                    .css({
                                        left: position.left,
                                        width: innerWidth,
                                    });
                            } else {
                                jQuery(this).removeClass("active");
                            }
                        });
                }
            });
    });
});

const nextTab = (nextWizardStep, currentActiveStep, next) => {
    if (nextWizardStep) {
        next.parents(".wizard-fieldset").removeClass("show", "400");
        currentActiveStep
            .removeClass("active")
            .addClass("activated")
            .next()
            .addClass("active", "400");
        next.parents(".wizard-fieldset")
            .next(".wizard-fieldset")
            .addClass("show", "400");
        next.parents(".wizard-fieldset")
            .next(".wizard-fieldset")
            .addClass("show", "400");
        jQuery(document)
            .find(".wizard-fieldset")
            .each(function () {
                if (jQuery(this).hasClass("show")) {
                    var formAtrr = jQuery(this).attr("data-tab-content");
                    jQuery(document)
                        .find(".form-wizard-steps .form-wizard-step-item")
                        .each(function () {
                            if (jQuery(this).attr("data-attr") == formAtrr) {
                                jQuery(this).addClass("active");
                                var innerWidth = jQuery(this).innerWidth();
                                var position = jQuery(this).position();
                                jQuery(document)
                                    .find(".form-wizard-step-move")
                                    .css({
                                        left: position.left,
                                        width: innerWidth,
                                    });
                            } else {
                                jQuery(this).removeClass("active");
                            }
                        });
                }
            });
    }
};
