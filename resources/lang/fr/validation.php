<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "accepted"        => "Le champ :attribute doit être accepté.",
    "active_url"      => "Le champ :attribute n'est pas une URL valide.",
    "after"           => "Le champ :attribute doit être une date postérieure au :date.",
    "after_or_equal"  => "Le champ :attribute doit être une date postérieure ou égale au :date.",
    "alpha"           => "Le champ :attribute ne peut contenir que des lettres.",
    "alpha_dash"      => "Le champ :attribute ne peut contenir que des lettres, nombres, tirets et tirets bas.",
    "alpha_num"       => "Le champ :attribute ne peut contenir que des lettres et des nombres.",
    "array"           => "Le champ :attribute doit être un tableau.",
    "before"          => "Le champ :attribute doit être une date antérieure au :date.",
    "before_or_equal" => "Le champ :attribute doit être une date antérieure ou égale au :date.",
    "between"         => [
        "numeric" => "Le champ :attribute doit être entre :min et :max.",
        "file"    => "Le champ :attribute doit être entre :min et :max kilooctets.",
        "string"  => "Le champ :attribute doit être entre :min et :max caractères.",
        "array"   => "Le champ :attribute doit avoir entre :min et :max éléments.",
    ],
    "boolean"        => "Le champ :attribute field doit être vrai ou faux.",
    "confirmed"      => "La confirmation du champ :attribute ne correspond pas.",
    "date"           => "Le champ :attribute n'est pas une date valide.",
    "date_equals"    => "Le champ :attribute doit être a date égale à :date.",
    "date_format"    => "Le champ :attribute ne correspond pas au format :format.",
    "different"      => "Le champ :attribute et :other doivent être différents.",
    "digits"         => "Le champ :attribute doit avoir :digits chiffres.",
    "digits_between" => "Le champ :attribute doit être entre :min et :max chiffres.",
    "dimensions"     => "Le champ :attribute n' pas une image aux dimensions valides.",
    "distinct"       => "La valeur du champ :attribute existe déjà.",
    "email"          => "Le champ :attribute doit être une adresse e-mail valide.",
    "exists"         => "La sélection du champ :attribute est invalide.",
    "file"           => "Le champ :attribute doit être un fichier.",
    "filled"         => "Le champ :attribute field doit avoir une valeur.",
    "gt"             => [
        "numeric" => "Le champ :attribute doit être supérieur à :value.",
        "file"    => "Le champ :attribute doit être supérieur à :value kilooctets.",
        "string"  => "Le champ :attribute doit avoir plus de :value caractères.",
        "array"   => "Le champ :attribute doit avoir plus de :value éléments.",
    ],
    "gte" => [
        "numeric" => "Le champ :attribute doit être supérieur ou égal à :value.",
        "file"    => "Le champ :attribute doit être supérieur ou égal à :value kilooctets.",
        "string"  => "Le champ :attribute doit avoir au moins :value caractères.",
        "array"   => "Le champ :attribute doit avoir au moins :value éléments.",
    ],
    "image"    => "Le champ :attribute doit être une image.",
    "in"       => "La sélection du champ :attribute est invalide.",
    "in_array" => "La valeur du champ :attribute n'existe pas dans :other.",
    "integer"  => "Le champ :attribute doit être un nombre entier.",
    "ip"       => "Le champ :attribute doit être une adresse IP valide.",
    "ipv4"     => "Le champ :attribute doit être une adresse IPv4 valide.",
    "ipv6"     => "Le champ :attribute doit être une adresse IPv6 valide.",
    "json"     => "Le champ :attribute doit être une chaîne JSON valide.",
    "lt"       => [
        "numeric" => "Le champ :attribute doit être inférieur à :value.",
        "file"    => "Le champ :attribute doit être inférieur à :value kilooctets.",
        "string"  => "Le champ :attribute doit avoir moins de :value caractères.",
        "array"   => "Le champ :attribute doit avoir moins de :value éléments.",
    ],
    "lte" => [
        "numeric" => "Le champ :attribute doit être inférieur ou égal à :value.",
        "file"    => "Le champ :attribute doit être inférieur ou égal à :value kilooctets.",
        "string"  => "Le champ :attribute doit avoir au plus :value caractères.",
        "array"   => "Le champ :attribute doit avoir au plus :value éléments.",
    ],
    "max" => [
        "numeric" => "Le champ :attribute ne peut pas être supérieur à :max.",
        "file"    => "Le champ :attribute ne peut pas être supérieur à :max kilooctets.",
        "string"  => "Le champ :attribute ne peux pas avoir plus de :max caractères.",
        "array"   => "Le champ :attribute ne peux pas avoir plus de :max éléments.",
    ],
    "mimes"     => "Le champ :attribute doit être un fichier parmi les types: :values.",
    "mimetypes" => "Le champ :attribute doit être un fichier parmi les types: :values.",
    "min"       => [
        "numeric" => "Le champ :attribute doit ne peut pas être inférieur à :min.",
        "file"    => "Le champ :attribute doit ne peut pas être inférieur à :min kilooctets.",
        "string"  => "Le champ :attribute ne peut pas avoir moins de :min caractères.",
        "array"   => "Le champ :attribute ne peut pas avoir moins de :min éléments.",
    ],
    "not_in"               => "La sélection du champ :attribute est invalide.",
    "not_regex"            => "Le format du champ :attribute est invalide.",
    "numeric"              => "Le champ :attribute doit être un nombre.",
    "present"              => "Le champ :attribute doit être present.",
    "regex"                => "Le format du champ :attribute est invalide.",
    "required"             => "Le champ :attribute est requis.",
    "required_if"          => "Le champ :attribute est requis si :other vaut :value.",
    "required_unless"      => "Le champ :attribute est requis sauf si :other est parmi :values.",
    "required_with"        => "Le champ :attribute est requis lorsque :values est présent.",
    "required_with_all"    => "Le champ :attribute est requis lorsque :values sont présents.",
    "required_without"     => "Le champ :attribute n'est pas requis lorsque :values n'est pas présent.",
    "required_without_all" => "Le champ :attribute n'est pas requis lorsqu'aucun de :values ne sont présents.",
    "same"                 => "Les champs :attribute et :other doivent être égaux.",
    "size"                 => [
        "numeric" => "Le champ :attribute doit être :size.",
        "file"    => "Le champ :attribute doit faire :size kilooctets.",
        "string"  => "Le champ :attribute doit faire :size caractères.",
        "array"   => "Le champ :attribute doit contenir :size éléments.",
    ],
    "starts_with" => "Le champ :attribute doit commencer avec une valeur parmi: :values",
    "string"      => "Le champ :attribute doit être une chaîne de caractères.",
    "timezone"    => "Le champ :attribute doit être un fuseau horaire valide.",
    "unique"      => "La valeur du champ :attribute existe déjà.",
    "uploaded"    => "Le champ :attribute a échoué lors de l'envoi.",
    "url"         => "Le format du champ :attribute est invalide.",
    "uuid"        => "Le champ :attribute doit être un UUID valide.",

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

    "custom" => [
        "attribute-name" => [
            "rule-name" => "custom-message",
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

    "recaptcha" => "Le système anti-robot a rejeté votre action !",

    "attributes" => [],

];
