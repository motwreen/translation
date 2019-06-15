<?php


if (!function_exists('transformUnderscoresToDotsInQueryString')) {

    function transformUnderscoresToDotsInQueryString($source)
    {
        $source = preg_replace_callback(
            '/(^|(?<=&))[^=[&]+/',
            function ($key) {
                return bin2hex(urldecode($key[0]));
            },
            $source
        );

        parse_str($source, $post);

        $result = array();
        foreach ($post as $key => $val) {
            $result[hex2bin($key)] = $val;
        }
        unset($result['_token']);
        return $result;
    }
}

if (!function_exists('varexport')) {

    function varexport($expression, $return = FALSE)
    {
        $export = var_export($expression, TRUE);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(["["] + $array));
        if ((bool)$return) return $export; else echo $export;
    }
}

if (!function_exists('increment_string')) {

    function increment_string($str, $separator = '-', $first = 1)
    {
        preg_match('/(.+)' . $separator . '([0-9]+)$/', $str, $match);
        return isset($match[2]) ? $match[1] . $separator . ($match[2] + 1) : $str . $separator . $first;
    }
}

if (!function_exists('make_slug')) {

    function make_slug($string = null, $lettersCount = 100, $separator = "-")
    {
        if (is_null($string)) {
            return "";
        }
        $string = trim($string);
        $string = mb_strtolower($string, "UTF-8");
        $string = preg_replace("/[^a-z0-9_\s-ءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]/u", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", $separator, $string);
        return \Illuminate\Support\Str::limit($string, $lettersCount, '');
    }
}
