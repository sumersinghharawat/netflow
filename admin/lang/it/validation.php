<?php

return [
    'accepted' => "L'attributo : deve essere accettato.",
    'accepted_if' => "L'attributo :deve essere accettato quando :other è :value.",
    'active_url' => "L'attributo :non è un URL valido.",
    'after' => ":attribute deve essere una data successiva a :date.",
    'after_or_equal' => "L'attributo : deve essere una data successiva o uguale a :date.",
    'alpha' => "L'attributo : deve contenere solo lettere.",
    'alpha_num' => "L'attributo : deve contenere solo lettere e numeri.",
    'array' => "L':attribute deve essere un array.",
    'before' => "L'attributo : deve essere una data precedente a :date.",
    'before_or_equal' => ":attribute deve essere una data precedente o uguale a :date.",
    'alpha_dash' => "L' :attribute deve contenere solo lettere, numeri, trattini e trattini bassi.",
    'between' => [
        'array' => "L' :attribute deve avere tra :min e :max elementi.",
        'file' => "L' :attribute deve essere compreso tra :min e :max kilobyte.",
        'numeric' => "L' :attribute deve essere compreso tra :min e :max.",
        'string' => "L' :attribute deve essere compreso tra :min e :max caratteri.",
    ],
    'boolean' => "Il campo :attribute deve essere vero o falso.",
    'confirmed' => "La conferma dell'attributo :non corrisponde.",
    'current_password' => "La password non è corretta.",
    'date' => "L':attribute non è una data valida.",
    'date_equals' => "L'attributo : deve essere una data uguale a :date.",
    'date_format' => ":attribute non corrisponde al formato :format.",
    'declined' => "L':attribute deve essere rifiutato.",
    'declined_if' => "L'attributo :deve essere rifiutato quando :other è :value.",
    'different' => "L'attributo :attribute e :altro devono essere diversi.",
    'digits' => "L' :attribute deve essere :digits cifre.",
    'digits_between' => "L' :attribute deve essere compreso tra :min e :max cifre.",
    'dimensions' => "L':attribute ha dimensioni dell'immagine non valide.",
    'distinct' => "Il campo :attribute ha un valore duplicato.",
    'email' => "L':attribute deve essere un indirizzo email valido.",
    'ends_with' => "L':attribute deve terminare con uno dei seguenti: :values.",
    'enum' => "L'attributo :selezionato non è valido.",
    'exists' => "L'attributo :selezionato non è valido.",
    'file' => "L'attributo : deve essere un file.",
    'filled' => "Il campo :attribute deve avere un valore.",
    'gt' => [
        'array' => "L'attributo :deve avere più di :elementi di valore.",
        'file' => "L'attributo : deve essere maggiore di :value kilobyte.",
        'numeric' => ":attribute deve essere maggiore di :value.",
        'string' => ":attribute deve essere maggiore di :value caratteri.",
    ],
    'gte' => [
        'array' => "L':attribute deve avere :value elementi o più.",
        'file' => "L'attributo : deve essere maggiore o uguale a :value kilobyte.",
        'numeric' => ":attribute deve essere maggiore o uguale a :value.",
        'string' => ":attribute deve essere maggiore o uguale a :value caratteri.",
    ],
    'image' => "L'attributo : deve essere un'immagine.",
    'in' => "L':attribute selezionato non è valido.",
    'in_array' => "Il campo :attribute non esiste in :other.",
    'integer' => "L'attributo : deve essere un numero intero.",
    'ip' => "L':attribute deve essere un indirizzo IP valido.",
    'ipv4' => "L'attributo : deve essere un indirizzo IPv4 valido.",
    'ipv6' => "L':attribute deve essere un indirizzo IPv6 valido.",
    'json' => "L'attributo : deve essere una stringa JSON valida.",
    'lt' => [
        'array' => "L'attributo : deve avere elementi inferiori a :value.",
        'file' => ":attribute deve essere inferiore a :value kilobyte.",
        'numeric' => ":attribute deve essere minore di :value.",
        'string' => ":attribute deve contenere meno di :value caratteri.",
    ],
    'lte' => [
        'array' => "L' :attribute non deve contenere più di :value elementi.",
        'file' => "L' :attribute deve essere minore o uguale a :value kilobyte.",
        'numeric' => "L'attributo : deve essere minore o uguale a :value.",
        'string' => "L'attributo :deve essere minore o uguale a :value caratteri.",
    ],
    'mac_address' => "L':attribute deve essere un indirizzo MAC valido.",
    'max' => [
        'array' => "L' :attribute non deve contenere più di :max elementi.",
        'file' => "L' :attribute non deve essere maggiore di :max kilobyte.",
        'numeric' => "L'attributo :non deve essere maggiore di :max.",
        'string' => "L' :attribute non deve essere maggiore di :max caratteri.",
    ],
    'mimes' => "L'attributo : deve essere un file di tipo: :values.",
    'mimetypes' => "L'attributo : deve essere un file di tipo: :values.",

    'min' => [
        'array' => "L'attributo : deve contenere almeno :min elementi.",
        'file' => "L'attributo : deve essere almeno :min kilobyte.",
        'numeric' => "L'attributo :deve essere almeno :min.",
        'string' => "L' :attribute deve contenere almeno :min caratteri.",

    ],
    'multiple_of' => "L'attributo :deve essere un multiplo di :value.",
    'not_in' => "L':attribute selezionato non è valido.",
    'not_regex' => "Il formato :attribute non è valido.",
    'numeric' => "L'attributo : deve essere un numero.",
    'password' => "La password non è corretta.",
    'present' => "Il campo :attribute deve essere presente.",
    'prohibited' => "Il campo :attribute è proibito.",
    'prohibited_if' => "Il campo :attribute è proibito quando :other è :value.",
    'prohibited_unless' => "Il campo :attribute è proibito a meno che :other non sia in :values.",
    'prohibits' => "Il campo :attribute impedisce la presenza di :other.",
    'regex' => "Il formato :attribute non è valido.",
    'required' => "Il campo :attribute è obbligatorio.",
    'required_array_keys' => "Il campo :attribute deve contenere voci per: :valori.",
    'required_if' => "Il campo :attribute è obbligatorio quando :other è :value.",
    'required_unless' => "Il campo :attribute è obbligatorio a meno che :other non sia in :values.",
    'required_with' => "Il campo :attribute è obbligatorio quando :values ​​è presente.",
    'required_with_all' => "Il campo :attribute è obbligatorio quando sono presenti :values.",
    'required_without' => "Il campo :attribute è obbligatorio quando :values ​​non è presente.",
    'required_without_all' => "Il campo :attribute è obbligatorio quando nessuno di :values è presente.",
    'same' => ":attribute e :other devono corrispondere.",

    'size' => [
        'array' => "L' :attribute deve contenere elementi :size.",
        'file' => "L'attributo : deve essere :size kilobyte.",
        'numeric' => "L' :attribute deve essere :size.",
        'string' => "L' :attribute deve contenere :size caratteri.",

    ],
    'starts_with' => "L'attributo : deve iniziare con uno dei seguenti: :values.",
    'string' => "L':attribute deve essere una stringa.",
    'timezone' => "L':attribute deve essere un fuso orario valido.",
    'unique' => "L':attribute è già stato preso.",
    'uploaded' => "Impossibile caricare l'attributo :",
    'url' => "L':attribute deve essere un URL valido.",
    'uuid' => "L':attribute deve essere un UUID valido.",


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
