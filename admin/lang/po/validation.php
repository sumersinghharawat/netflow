<?php

return [
    'accepted' => ":attribute musi zostać zaakceptowany.",
    'accepted_if' => ":attribute musi być zaakceptowany, gdy :other ma wartość :value.",
    'active_url' => ":attribute nie jest prawidłowym adresem URL.",
    'after' => ":attribute musi być datą po :date.",
    'after_or_equal' => ":attribute musi być datą po lub równą :date.",
    'alpha' => ":attribute może zawierać tylko litery.",
    'alpha_num' => ":attribute może zawierać tylko litery i cyfry.",
    'array' => ":attribute musi być tablicą.",
    'before' => ":attribute musi być datą przed :date.",
    'before_or_equal' => ":attribute musi być datą wcześniejszą lub równą :date.",
    'alpha_dash' => ':attribute może zawierać tylko litery, cyfry, myślniki i podkreślenia.',
    'between' => [
        'array' => "Pole :attribute musi zawierać elementy od :min do :max.",
        'file' => ":attribute musi zawierać się w przedziale od :min do :max kilobajtów.",
        'numeric' => ":attribute musi zawierać się w przedziale od :min do :max.",
        'string' => ":attribute musi zawierać się w przedziale od :min do :max znaków.",
    ],
    'boolean' => "Pole :attribute musi mieć wartość true lub false.",
    'confirmed' => "Potwierdzenie :attribute nie pasuje.",
    'current_password' => "Hasło jest błędne.",
    'date' => ":attribute nie jest prawidłową datą.",
    'date_equals' => ":attribute musi być datą równą :data.",
    'date_format' => ":attribute nie pasuje do formatu :format.",
    'declined' => ":attribute musi zostać odrzucony.",
    'declined_if' => ":attribute musi zostać odrzucony, gdy :inne to :value.",
    'different' => "Parametry :attribute i :other muszą być różne.",
    'digits' => ":attribute musi składać się z cyfr :digits.",
    'digits_between' => ":attribute musi zawierać się między cyframi :min i :max.",
    'dimensions' => ":attribute ma nieprawidłowe wymiary obrazu.",
    'distinct' => "Pole :attribute ma zduplikowaną wartość.",
    'email' => ":attribute musi być prawidłowym adresem e-mail.",
    'ends_with' => ":attribute musi kończyć się jednym z następujących: :values.",
    'enum' => "Wybrany :attribute jest nieprawidłowy.",
    'exists' => "Wybrany :attribute jest nieprawidłowy.",
    'file' => ":attribute musi być plikiem.",
    'filled' => "Pole :attribute musi mieć wartość.",
    'gt' => [
        'array' => "Opcja :attribute musi zawierać więcej elementów niż :value.",
        'file' => ":attribute musi być większy niż :value kilobajtów.",
        'numeric' => ":attribute musi być większy niż :value.",
        'string' => ":attribute musi być większy niż :value znaków.",
    ],
    'gte' => [
        'array' => ":attribute musi zawierać elementy :value lub więcej.",
        'file' => "Wartość :attribute musi być większa lub równa :value kilobajtów.",
        'numeric' => ":attribute musi być większy lub równy :value.",
        'string' => "Wartość :attribute musi być większa lub równa :value znaków.",
    ],
    'image' => ":attribute musi być obrazem.",
    'in' => "Wybrany :attribute jest nieprawidłowy.",
    'in_array' => "Pole :attribute nie istnieje w :other.",
    'integer' => ":attribute musi być liczbą całkowitą.",
    'ip' => ":attribute musi być poprawnym adresem IP.",
    'ipv4' => ":attribute musi być prawidłowym adresem IPv4.",
    'ipv6' => ":attribute musi być prawidłowym adresem IPv6.",
    'json' => ":attribute musi być prawidłowym ciągiem JSON.",
    'lt' => [
        'array' => ":attribute musi zawierać mniej niż :value elementów.",
        'file' => ":attribute musi być mniejszy niż :value kilobajtów.",
        'numeric' => ":attribute musi być mniejszy niż :value.",
        'string' => "Wartość :attribute musi być mniejsza niż :value znaków.",
    ],
    'lte' => [
        'array' => ":attribute nie może zawierać więcej niż :value pozycji.",
        'file' => "Wartość :attribute musi być mniejsza lub równa :value kilobajtów.",
        'numeric' => ":attribute musi być mniejszy lub równy :value.",
        'string' => ":attribute musi być mniejszy lub równy :value znakom.",
    ],
    'mac_address' => ":attribute musi być poprawnym adresem MAC.",
    'max' => [
        'array' => ":attribute nie może zawierać więcej niż :max elementów.",
        'file' => ":attribute nie może być większy niż :max kilobajtów.",
        'numeric' => ":attribute nie może być większy niż :max.",
        'string' => "Wartość :attribute nie może być większa niż :max znaków.",
    ],
    'mimes' => ":attribute musi być plikiem typu: :values.",
    'mimetypes' => ":attribute musi być plikiem typu: :values.",

    'min' => [
        'array' => ":attribute musi zawierać co najmniej :min elementów.",
        'file' => ":attribute musi mieć co najmniej :min kilobajtów.",
        'numeric' => ":attribute musi wynosić co najmniej :min.",
        'string' => ":attribute musi mieć co najmniej :min znaków.",

    ],
    'multiple_of' => ":attribute musi być wielokrotnością :values.",
    'not_in' => "Wybrany :attribute jest nieprawidłowy.",
    'not_regex' => "Format :attribute jest nieprawidłowy.",
    'numeric' => ":attribute musi być liczbą.",
    'password' => "Hasło jest błędne.",
    'present' => "Pole :attribute musi być obecne.",
    'prohibited' => "Pole :attribute jest zabronione.",
    'prohibited_if' => "Pole :attribute jest zabronione, gdy :other to :value.",
    'prohibited_unless' => "Pole :attribute jest zabronione, chyba że :other znajduje się w :values.",
    'prohibits' => "Pole :attribute zabrania obecności :other.",
    'regex' => "Format :attribute jest nieprawidłowy.",
    'required' => "Pole :attribute jest wymagane.",
    'required_array_keys' => "Pole :attribute musi zawierać wpisy dla: :values.",
    'required_if' => "Pole :attribute jest wymagane, gdy :other ma wartość :value.",
    'required_unless' => "Pole :attribute jest wymagane, chyba że :other jest w :values.",
    'required_with' => "Pole :attribute jest wymagane, gdy obecne jest :values.",
    'required_with_all' => "Pole :attribute jest wymagane, gdy obecne są wartości :values.",
    'required_without' => "Pole :attribute jest wymagane, gdy nie ma wartości :values.",
    'required_without_all' => "Pole :attribute jest wymagane, gdy żadna z wartości :value nie jest obecna.",
    'same' => ":attribute i :other muszą się zgadzać.",

    'size' => [
        'array' => ":attribute musi zawierać elementy :size.",
        'file' => ":attribute musi mieć :size kilobajtów.",
        'numeric' => ":attribute musi być :rozmiar.",
        'string' => "Pole :attribute musi składać się ze znaków :size.",

    ],
    'starts_with' => "Pole :attribute musi zaczynać się od jednego z następujących: :values.",
    'string' => ":attribute musi być ciągiem znaków.",
    'timezone' => ":attribute musi być prawidłową strefą czasową.",
    'unique' => ":attribute został już zajęty.",
    'uploaded' => "Nie udało się przesłać :attribute.",
    'url' => ":attribute musi być prawidłowym adresem URL.",
    'uuid' => ":attribute musi być prawidłowym identyfikatorem UUID.",


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
