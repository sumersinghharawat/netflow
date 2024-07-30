

export const loginUsernameValidator = (item, t) => {
    const rules = {
        required: {
            value: true,
            message: t('this_field_is_required')
        },
    };

    if (item.type === 'text') {
        rules.minLength = {
            value: item.validation.max,
            message: `${t('min_length_is')} ${item.validation.max}`,
        };
        rules.maxLength = {
            value: item.validation.min,
            message: `${t('max_length_is')} ${item.validation.min} `,
        };
        rules.pattern = {
            value: /^[A-Za-z0-9]+$/,
            message: t("invalid_format"),
        }
    } else if (item.type === 'password') {
        rules.minLength = {
            value: item.validation.minLength,
            message: `${t('min_length_is')} ${item.validation.minLength}`,
        };
    }
    return rules;
}

export const loginPasswordValidator = (item, t) => {
    const rules = {
        required: {
            value: true,
            message: t('this_field_is_required')
        },
    };
    if (item.validation.minLength) {
        rules.minLength = {
            value: item.validation.minLength,
            message: `${t('min_length_is')} ${item.validation.minLength}`
        }
    }
    if (item.validation.specialChar) {
        rules.pattern = {
            value: /[!@#$%^&*()_+\-=\[\]{};':"|,.<>?]/,
            message: t('must_contain_at_least_one_special_character')
        };
    }
    if (item.validation.number) {
        rules.patternNumber = {
            value: /[0-9]/,
            message: t('must_contain_at_least_one_number')
        };
    }
    if (item.validation.capital) {
        rules.patternCapital = {
            value: /[A-Z]/,
            message: t('must_contain_at_least_one_capital_letter')
        };
    }

    return rules;
}

export const passwordRules = (item) => {
    const rules = []
    if (item.minLength) {
        rules.push('minLength')
    }
    if (item.spChar) {
        rules.push('specialChar');
    }
    if (item.number) {
        rules.push('number');
    }
    if (item.mixedCase) {
        rules.push('capital');
    }
    // if (item.match) {
        rules.push('match');
    // }

    return rules
}

export const validateAge = (value, t) => {
    const currentDate = new Date();
    const selectedDate = new Date(value);
    const minAge = 18; // Change this to your desired minimum age
    if (currentDate.getFullYear() - selectedDate.getFullYear() < minAge) {
        return t("you_must_be_at_least_18_years_old.");
    }

    return true;
};

export const forgetPasswordValidator = (item, t) => {
    const rules = {
        required: {
            value: true,
            message: t('this_field_is_required')
        },
    };
    if (item.passwordPolicy.minLength) {
        rules.minLength = {
            value: item.passwordPolicy.minLength,
            message: `${t('min_length_is')} ${item.passwordPolicy.minLength}`
        }
    }
    if (item.passwordPolicy.spChar) {
        rules.pattern = {
            value: /[!@#$%^&*()_+\-=\[\]{};':"|,.<>?]/,
            message: t('must_contain_at_least_one_special_character')
        };
    }
    if (item.passwordPolicy.number) {
        rules.patternNumber = {
            value: /[0-9]/,
            message: t('must_contain_at_least_one_number')
        };
    }
    if (item.passwordPolicy.mixedCase) {
        rules.patternCapital = {
            value: /[A-Z]/,
            message: t('must_contain_at_least_one_capital_letter')
        };
    }

    return rules;
}
