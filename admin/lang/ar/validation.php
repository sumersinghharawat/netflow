<?php

return [
    'accepted' => "يجب قبول :attribute.",
    'accepted_if' => "يجب قبول :attribute عندما :other هو :value.",
    'active_url' => ":attribute ليست عنوان URL صالحًا.",
    'after' => "يجب أن تكون :attribute تاريخًا بعد :date.",
    'after_or_equal' => "يجب أن تكون :attribute تاريخًا بعد :date أو مساويًا له.",
    'alpha' => "يجب أن تحتوي :attribute على أحرف فقط.",
    'alpha_dash' => 'يجب أن تحتوي :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num' => "يجب أن تحتوي :attribute على أحرف وأرقام فقط.",
    'array' => "يجب أن تكون :attribute مصفوفة.",
    'before' => "يجب أن تكون :attribute تاريخًا قبل :date.",
    'before_or_equal' => "يجب أن تكون :attribute تاريخًا يسبق :date أو مساويًا له.",
    'between' => [
        'array' => "يجب أن تحتوي :attribute على ما بين :min و :max العناصر.",
        'file' => "يجب أن تكون :attribute بين :min و :max كيلوبايت.",
        'numeric' => "يجب أن تكون :attribute بين :min و :max.",
        'string' => "يجب أن تكون :attribute بين :min و :max من الأحرف.",
    ],
    'boolean' => "يجب أن يكون حقل :attribute صحيحًا أو خطأً.",
    'confirmed' => "تأكيد :attribute غير مطابق.",
    'current_password' => "كلمة المرور غير صحيحة.",
    'date' => ":attribute ليست تاريخًا صالحًا.",
    'date_equals' => "يجب أن تكون :attribute تاريخًا مساويًا لـ :date.",
    'date_format' => ":attribute لا تطابق التنسيق :format.",
    'declined' => "يجب رفض :attribute.",
    'declined_if' => "يجب رفض :attribute عندما :other هو :value.",
    'different' => "يجب أن يكون :attribute و :other مختلفين.",
    'digits' => "يجب أن تكون :attribute أرقامًا وأرقامًا.",
    'digits_between' => "يجب أن تكون :attribute بين :min و :max digits.",
    'dimensions' => ":attribute لها أبعاد صورة غير صالحة.",
    'distinct' => "يحتوي حقل :attribute على قيمة مكررة.",
    'email' => "يجب أن تكون :attribute عنوان بريد إلكتروني صالحًا.",
    'ends_with' => "يجب أن تنتهي :attribute بأحد القيم :attribute",
    'enum' => ":attribute المحددة :غير صالحة.",
    'exists' => ":attribute المحددة :غير صالحة.",
    'file' => "يجب أن تكون :attribute ملفًا.",
    'filled' => "يجب أن يحتوي حقل :attribute على قيمة.",
    'gt' => [
        'array' => "يجب أن تحتوي :attribute على أكثر من :عناصر القيمة.",
        'file' => "يجب أن تكون :attribute أكبر من :value كيلوبايت.",
        'numeric' => "يجب أن تكون :attribute أكبر من :value.",
        'string' => "يجب أن تكون :attribute أكبر من :value القيمة.",
    ],
    'gte' => [
        'array' => "يجب أن تحتوي :attribute على عناصر قيمة أو أكثر.",
        'file' => "يجب أن تكون :attribute أكبر من أو تساوي :value كيلوبايت.",
        'numeric' => "يجب أن تكون :attribute أكبر من أو تساوي :value.",
        'string' => "يجب أن تكون :attribute أكبر من أو تساوي :value القيمة.",
    ],
    'image' => "يجب أن تكون :attribute صورة.",
    'in' => ":attribute المحددة :غير صالحة.",
    'in_array' => "حقل :attribute غير موجود في :أخرى.",
    'integer' => "يجب أن تكون :attribute عددًا صحيحًا.",
    'ip' => "يجب أن تكون :attribute عنوان IP صالحًا.",
    'ipv4' => "يجب أن تكون :attribute عنوان IPv4 صالحًا.",
    'ipv6' => "يجب أن تكون :attribute عنوان IPv6 صالحًا.",
    'json' => "يجب أن تكون :attribute سلسلة JSON صالحة.",
    'lt' => [
        'array' => "يجب أن تحتوي :attribute على أقل من عناصر :value.",
        'file' => "يجب أن تكون :attribute أقل من :value كيلوبايت.",
        'numeric' => "يجب أن تكون :attribute أقل من :value.",
        'string' => "يجب أن تكون :attribute أقل من :value القيمة.",
    ],
    'lte' => [
        'array' => "يجب ألا تحتوي :attribute على أكثر من عناصر :value.",
        'file' => "يجب أن تكون :attribute أقل من أو تساوي :value كيلوبايت.",
        'numeric' => "يجب أن تكون :attribute أقل من أو تساوي :value.",
        'string' => "يجب أن تكون :attribute أقل من أو تساوي :value القيمة.",
    ],
    'mac_address' => "يجب أن تكون :attribute عنوان MAC صالحًا.",
    'max' => [
        'array' => "يجب ألا تحتوي :attribute على أكثر من :max items.",
        'file' => "يجب ألا تكون :attribute أكبر من :أقصى كيلوبايت.",
        'numeric' => "يجب ألا تكون :attribute أكبر من :max.",
        'string' => "يجب ألا تكون :attribute أكبر من :الحد الأقصى من الأحرف.",
    ],
    'mimes' => "يجب أن تكون :attribute ملفًا من النوع :value.",
    'mimetypes' => "يجب أ ن تكون :attribute ملفًا من النوع :value.",

    'min' => [
        'array' => "يجب أن تحتوي :attribute على الأقل على :min من العناصر.",
        'file' => "يجب أن تكون :attribute على الأقل :دقيقة كيلوبايت.",
        'numeric' => "يجب أن تكون :attribute :min.",
        'string' => "يجب ألا تقل :attribute عن :min حرفًا.",

    ],
    'multiple_of' => "يجب أن تكون :attribute من مضاعفات :value.",
    'not_in' => ":attribute المحددة :غير صالحة.",
    'not_regex' => "تنسيق :attribute غير صالح.",
    'numeric' => "يجب أن تكون :attribute رقمًا.",
    'password' => "كلمة المرور غير صحيحة.",
    'present' => "يجب أن يكون حقل :attribute موجودًا.",
    'prohibited' => "حقل :attribute محظور.",
    'prohibited_if' => "يُحظر حقل :attribute عندما :size هو :value.",
    'prohibited_unless' => "يُحظر حقل :attribute إلا إذا كان الآخر في :value.",
    'prohibits' => "يحظر حقل :attribute وجود الآخرين.",
    'regex' => "تنسيق :attribute غير صالح.",
    'required' => " :حقل :attribute مطلوب.",
    'required_array_keys' => "يجب أن يحتوي حقل :attribute على إدخالات لـ :values.",
    'required_if' => "يكون حقل :attribute مطلوبًا عندما :size هو :value.",
    'required_unless' => "حقل :attribute مطلوب إلا إذا كان الآخر في :values.",
    'required_with' => "يكون حقل :attribute مطلوبًا عندما تكون :value موجودة.",
    'required_with_all' => "يكون حقل :attribute مطلوبًا عندما تكون :value موجودة.",
    'required_without' => "يكون حقل :attribute مطلوبًا عندما :value غير موجودة.",
    'required_without_all' => "يكون حقل :attribute مطلوبًا في حالة عدم وجود أي من :values.",
    'same' => "يجب أن يتطابق :attribute و :size.",

    'size' => [
        'array' => "يجب أن تحتوي :attribute على :حجم العناصر.",
        'file' => "يجب أن تكون :attribute الحجم كيلوبايت.",
        'numeric' => "يجب أن تكون :attribute الحجم.",
        'string' => "يجب أن تكون :attribute حجم الأحرف.",

    ],
    'starts_with' => "يجب أن تبدأ :attribute بأحد القيم :attribute",
    'string' => "يجب أن تكون :attribute سلسلة.",
    'timezone' => "يجب أن تكون :attribute منطقة زمنية صالحة.",
    'unique' => "تم استخدام :attribute بالفعل.",
    'uploaded' => "فشل تحميل :attribute.",
    'url' => "يجب أن تكون :attribute عنوان URL صالحًا.",
    'uuid' => "يجب أن تكون :attribute UUID صالحًا.",


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
