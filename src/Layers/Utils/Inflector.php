<?php

namespace Sienekib\Layers\Utils;

class Inflector 
{
    private static $rules = [
        'en' => [
            'plural' => [
                '/(quiz)$/i' => '\1zes',
                '/^(ox)$/i' => '\1en',
                '/([m|l])ouse$/i' => '\1ice',
                '/(matr|vert|ind)(ix|ex)$/i' => '\1ices',
                '/(x|ch|ss|sh)$/i' => '\1es',
                '/([^aeiouy]|qu)y$/i' => '\1ies',
                '/(hive)$/i' => '\1s',
                '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
                '/sis$/i' => 'ses',
                '/([ti])um$/i' => '\1a',
                '/(buffal|tomat|potat)o$/i' => '\1oes',
                '/(bu)s$/i' => '\1ses',
                '/(alias|status)$/i' => '\1es',
                '/(octop|vir)us$/i' => '\1i',
                '/(ax|test)is$/i' => '\1es',
                '/s$/i' => 's',
                '/$/' => 's'
            ],
            'singular' => [
                '/(quiz)zes$/i' => '\1',
                '/(matr|vert|ind)ices$/i' => '\1ix',
                '/^(ox)en$/i' => '\1',
                '/(alias|status)es$/i' => '\1',
                '/(octop|vir)i$/i' => '\1us',
                '/(cris|ax|test)es$/i' => '\1is',
                '/(shoe)s$/i' => '\1',
                '/(o)es$/i' => '\1',
                '/(bus)es$/i' => '\1',
                '/([m|l])ice$/i' => '\1ouse',
                '/(x|ch|ss|sh)es$/i' => '\1',
                '/(m)ovies$/i' => '\1ovie',
                '/(s)eries$/i' => '\1eries',
                '/([^aeiouy]|qu)ies$/i' => '\1y',
                '/([lr])ves$/i' => '\1f',
                '/(tive)s$/i' => '\1',
                '/(hive)s$/i' => '\1',
                '/([^f])ves$/i' => '\1fe',
                '/(^analy)ses$/i' => '\1sis',
                '/([ti])a$/i' => '\1um',
                '/s$/i' => ''
            ]
        ],
        'fr' => [
            'plural' => [
                '/al$/i' => 'aux',
                '/eau$/i' => 'eaux',
                '/$/' => 's'
            ],
            'singular' => [
                '/aux$/i' => 'al',
                '/eaux$/i' => 'eau',
                '/s$/i' => ''
            ]
        ],
        'pt' => [
            'plural' => [
                '/ão$/i' => 'ões',
                '/l$/i' => 'is',
                '/m$/i' => 'ns',
                '/r$/i' => 'res',
                '/s$/i' => 'ses',
                '/$/' => 's'
            ],
            'singular' => [
                '/ões$/i' => 'ão',
                '/is$/i' => 'l',
                '/ns$/i' => 'm',
                '/res$/i' => 'r',
                '/ses$/i' => 's',
                '/s$/i' => ''
            ]
        ]
    ];

    public static function pluralize($word, $lang) {
        if (isset(self::$rules[$lang]['plural'])) {
            foreach (self::$rules[$lang]['plural'] as $pattern => $replacement) {
                if (preg_match($pattern, $word)) {
                    return preg_replace($pattern, $replacement, $word);
                }
            }
        }
        return $word;
    }

    public static function singularize($word, $lang) {
        if (isset(self::$rules[$lang]['singular'])) {
            foreach (self::$rules[$lang]['singular'] as $pattern => $replacement) {
                if (preg_match($pattern, $word)) {
                    return preg_replace($pattern, $replacement, $word);
                }
            }
        }
        return $word;
    }
}

