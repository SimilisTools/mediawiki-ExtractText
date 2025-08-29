<?php

namespace MediaWiki\Extension\ExtractText;

use ExtExtractText;

class Hooks
{
    private static $realObj = null;

    public static function onParserFirstCallInit($parser)
    {
        $parser->setFunctionHook(
            'extracttext',
            [ self::class, 'extracttext' ],
            \SFH_OBJECT_ARGS
        );
        $parser->setFunctionHook(
            'extractpagetext',
            [ self::class, 'extractpagetext' ],
            \SFH_OBJECT_ARGS
        );
        return true;
    }

    public static function onParserClearState($parser)
    {
        if (self::$realObj !== null) {
            self::$realObj->clearState($parser);
        }
        return true;
    }

    public static function extracttext(...$args)
    {
        if (self::$realObj === null) {
            self::$realObj = new ExtExtractText();
            self::$realObj->clearState($args[0]);
        }
        return call_user_func_array([ self::$realObj, 'extracttext' ], $args);
    }

    public static function extractpagetext(...$args)
    {
        if (self::$realObj === null) {
            self::$realObj = new ExtExtractText();
            self::$realObj->clearState($args[0]);
        }
        return call_user_func_array([ self::$realObj, 'extractpagetext' ], $args);
    }
}
