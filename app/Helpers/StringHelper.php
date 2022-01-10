<?php

namespace App\Helpers;

class StringHelper
{
    /**
     * @param string $text
     * @param string $rule
     * @return mixed
     */
    public static function doRegex(string $text, string $rule)
    {
        preg_match_all($rule, $text, $result);

        return $result;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function clearPageContent(string $text): string
    {
        $rules = [
            "\r\n",
            "\n",
            "\r",
            "\t",
            '&nbsp;',
        ];

        return trim(str_replace($rules, '', $text));
    }

    /**
     * @param string $text
     * @param $regex
     * @param string $replace
     * @return string|string[]|null
     */
    public static function replaceRegex(string $text, $regex, string $replace)
    {
        return preg_replace($regex, $replace, $text);
    }
}
