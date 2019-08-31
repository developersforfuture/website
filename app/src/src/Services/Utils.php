<?php

namespace App\Services;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class Utils
{
    /**
     * @param string $string
     * @return string
     */
    public static function createSeoUrl($string)
    {
        $string = strtolower($string);
        $string = str_replace('ä', 'ae', $string);
        $string = str_replace('ö', 'oe', $string);
        $string = str_replace('ü', 'ue', $string);
        $string = str_replace('ß', 'ss', $string);
        $string = static::replaceByPatternOrFallback("/[^a-z0-9_\s-]/", "", $string);
        $string = static::replaceByPatternOrFallback("/[\s-]+/", " ", $string);
        $string = static::replaceByPatternOrFallback("/[\s_]/", "-", $string);

        return $string;
    }

    private static function replaceByPatternOrFallback(string $pattern, string $replacement, string $subject): string
    {
        $replaced = preg_replace($pattern, $replacement, $subject);

        if ($replaced === null) {
            return $subject;
        }

        return $replaced;
    }
}
