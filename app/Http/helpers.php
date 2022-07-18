<?php

// function translateLang($text)
// {
//     // What languages do we support
//     $available_langs = array('EN', 'CN');

//     if (session()->has('lang')) {
//         // check if the language is one we support
//         if (in_array(session()->get('lang'), $available_langs)) { } else {
//             session()->put('lang', 'EN');
//         }
//     } else {
//         session()->put('lang', 'EN');
//     }

//     // Include active language
//     include(storage_path() . '/app/lang/lang.' . session()->get('lang') . '.php');

//     if (isset($lang[$text])) {
//         return $lang[$text];
//     } else {
//         return $text;
//     }
// }


function translateLang($texts = array('EN', 'CN'))
{
    // What languages do we support
    $available_langs = array('EN', 'CN');

    $k = 1;

    if (session()->has('lang')) {
        // check if the language is one we support
        if (in_array(session()->get('lang'), $available_langs)) {
            $k = array_search(session()->get('lang'), $available_langs);
        }
    }

    if (isset($texts[$k])) {
        return $texts[$k];
    } else {
        return '';
    }
}
