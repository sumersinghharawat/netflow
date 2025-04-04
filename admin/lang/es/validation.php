<?php

return [
    'accepted' => "El :attribute debe ser aceptado.",
    'accepted_if' => "El :attribute debe aceptarse cuando :other es :value",
    'active_url' => "El :attribute no es una URL válida.",
    'after' => "El :attribute debe ser una fecha posterior a :date.",
    'after_or_equal' => "El :attribute debe ser una fecha posterior o igual a :date.",
    'alpha' => "El atributo :solo debe contener letras.",
    'alpha_dash' => 'El :attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num' => "El atributo :solo debe contener letras y números.",
    'array' => "El :attribute debe ser una matriz.",
    'before' => "El :attribute debe ser una fecha anterior a :date.",
    'before_or_equal' => "El :attribute debe ser una fecha anterior o igual a :date.",
    'between' => [
        'array' => "El :attribute debe tener entre :min y :max elementos.",
        'file' => "El :attribute debe estar entre :min y :max kilobytes.",
        'numeric' => "El :attribute debe estar entre :min y :max.",
        'string' => "El :attribute debe tener entre :min y :max caracteres.",
    ],
    'boolean' => "El campo :attribute debe ser verdadero o falso.",
    'confirmed' => "La confirmación de :attribute no coincide.",
    'current_password' => "La contraseña es incorrecta.",
    'date' => "El :attribute no es una fecha válida.",
    'date_equals' => "El :attribute debe ser una fecha igual a :date.",
    'date_format' => "El :attribute no coincide con el formato :formato.",
    'declined' => "El :attribute debe ser rechazado.",
    'declined_if' => "El :attribute debe rechazarse cuando :other es :value",
    'different' => "El :attribute y :other deben ser diferentes.",
    'digits' => "El :attribute debe ser :dígitos dígitos.",
    'digits_between' => "El :attribute debe estar entre :min y :max dígitos.",
    'dimensions' => "El :attribute tiene dimensiones de imagen no válidas.",
    'distinct' => "El campo :attribute tiene un valor duplicado.",
    'email' => "El :attribute debe ser una dirección de correo electrónico válida.",
    'ends_with' => "El :attribute debe terminar con uno de los siguientes: :values.",
    'enum' => "El :attribute seleccionado no es válido.",
    'exists' => "El :attribute seleccionado no es válido.",
    'file' => "El :attribute debe ser un archivo.",
    'filled' => "El campo :attribute debe tener un valor.",
    'gt' => [
        'array' => "El :attribute debe tener más de :value de valor.",
        'file' => "El :attribute debe ser mayor que :value kilobytes.",
        'numeric' => "El :attribute debe ser mayor que :value.",
        'string' => "El :attribute debe ser mayor que los :valuees.",
    ],
    'gte' => [
        'array' => "El :attribute debe tener :value de valor o más.",
        'file' => "El :attribute debe ser mayor o igual que :value en kilobytes.",
        'numeric' => "El :attribute debe ser mayor o igual que :value.",
        'string' => "El :attribute debe ser mayor o igual que :value caracteres.",
    ],
    'image' => "El :attribute debe ser una imagen.",
    'in' => "El :attribute seleccionado no es válido.",
    'in_array' => "El campo :attribute no existe en :otro.",
    'integer' => "El :attribute debe ser un número entero.",
    'ip' => "El :attribute debe ser una dirección IP válida.",
    'ipv4' => "El :attribute debe ser una dirección IPv4 válida.",
    'ipv6' => "El :attribute debe ser una dirección IPv6 válida.",
    'json' => "El :attribute debe ser una cadena JSON válida.",
    'lt' => [
        'array' => "El :attribute debe tener menos de :value de valor.",
        'file' => "El :attribute debe tener menos de :value kilobytes.",
        'numeric' => "El :attribute debe ser menor que :value.",
        'string' => "El :attribute debe tener menos de :value de caracteres.",
    ],
    'lte' => [
        'array' => "El :attribute no debe tener más de :value de valor.",
        'file' => "El :attribute debe ser menor o igual que :value en kilobytes.",
        'numeric' => "El :attribute debe ser menor o igual que :value.",
        'string' => "El :attribute debe ser menor o igual que :value caracteres.",
    ],
    'mac_address' => "El :attribute debe ser una dirección MAC válida.",
    'max' => [
        'array' => "El :attribute no debe tener más de :máx elementos.",
        'file' => "El :attribute no debe ser mayor que :max kilobytes.",
        'numeric' => "El :attribute no debe ser mayor que :max.",
        'string' => "El :attribute no debe tener más de :máx caracteres.",
    ],
    'mimes' => "El :attribute debe ser un archivo de tipo: :values.",
    'mimetypes' => "El :attribute debe ser un archivo de tipo: :values.",

    'min' => [
        'array' => "El :attribute debe tener al menos :min elementos.",
        'file' => "El :attribute debe tener al menos :min kilobytes.",
        'numeric' => "El :attribute debe ser al menos :min.",
        'string' => "El :attribute debe tener al menos :min caracteres.",

    ],
    'multiple_of' => "El :attribute debe ser un múltiplo de :value",
    'not_in' => "El :attribute seleccionado no es válido.",
    'not_regex' => "El formato de :attribute no es válido.",
    'numeric' => "El :attribute debe ser un número.",
    'password' => "La contraseña es incorrecta.",
    'present' => "El campo :attribute debe estar presente.",
    'prohibited' => "El campo :attribute está prohibido.",
    'prohibited_if' => "El campo :attribute está prohibido cuando :other es :value",
    'prohibited_unless' => "El campo :attribute está prohibido a menos que :other esté en :values.",
    'prohibits' => "El campo :attribute prohíbe que :other esté presente.",
    'regex' => "El formato de :attribute no es válido.",
    'required' => "El campo :attribute es obligatorio.",
    'required_array_keys' => "El campo :attribute debe contener entradas para: :values.",
    'required_if' => "El campo :attribute es obligatorio cuando :other es :value",
    'required_unless' => "El campo :attribute es obligatorio a menos que :other esté en :values.",
    'required_with' => "El campo :attribute es obligatorio cuando :values está presente.",
    'required_with_all' => "El campo :attribute es obligatorio cuando los :values están presentes.",
    'required_without' => "El campo :attribute es obligatorio cuando :values no está presente.",
    'required_without_all' => "El campo :attribute es obligatorio cuando ninguno de los :values está presente.",
    'same' => "El :attribute y :other deben coincidir.",

    'size' => [
        'array' => "El :attribute debe contener :value de tamaño.",
        'file' => "El :attribute debe ser :size kilobytes.",
        'numeric' => "El :attribute debe ser :tamaño.",
        'string' => "El :attribute debe tener :tamaño de caracteres.",

    ],
    'starts_with' => "El :attribute debe comenzar con uno de los siguientes: :values.",
    'string' => "El :attribute debe ser una cadena.",
    'timezone' => "El :attribute debe ser una zona horaria válida.",
    'unique' => "El :attribute ya ha sido tomado.",
    'uploaded' => "El :attribute no se pudo cargar.",
    'url' => "El :attribute debe ser una URL válida.",
    'uuid' => "El :attribute debe ser un UUID válido.",


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
