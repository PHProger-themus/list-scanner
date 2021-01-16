<?php

namespace phproger\scanner;

use View;
use phproger\scanner\ScannerInterface;

class Scanner implements ScannerInterface {

    public static function find(string $input, $source, int $count = 1, int $mode = self::SEARCH_BY_VALUES, string $delimiter = ' ', int $looking_from = self::ARRAY_BEGIN) {

        $distance = -1;
        $f_wip = '';
        $f_key = '';
        $found = [];
        $found_part = true;

        $wc = 0;

        if (gettype($source) == "string") {
            $source = str_replace('-', ' ', trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $source)));
            $source = explode(' ', $source);
        }

        $input = explode($delimiter, $input);
        $input = array_unique($input);

        switch ($looking_from) {
            case self::ARRAY_END:
                $input = array_reverse($input);
                break;
            case self::ARRAY_RAND:
                shuffle($input);
                break;
        }

        $last_word = $input[count($input) - 1];

        if ($count > count($source))
            View::setError('Невозможно получить больше слов, чем содержит источник');

        foreach ($input as $keyword) {

            $found_part = true;

            while ($found_part) {

                if ($wc < $count) {

                    foreach ($source as $key => $word) {

                        if ($mode == 1)
                            $pos = strripos($word, $keyword);
                        else
                            $pos = strripos($key, $keyword);

                        if ($pos != '') {

                            $found_part = true;
                            $found[$key] = $word;
                            self::deleteValue($source, $key, $word, $mode);

                            $wc++;
                            break;
                        }

                        $found_part = false;
                    }
                } else
                    break;
            }
        }

        foreach ($input as $keyword) {

            if ($wc < $count) {

                if ($keyword == $last_word)
                    $final_wc = $count;
                else
                    $final_wc = $wc + 1;

                while ($wc < $final_wc) {
                    foreach ($source as $key => $word) {

                        if ($mode == 1)
                            $lev = levenshtein($keyword, $word);
                        else
                            $lev = levenshtein($keyword, $key);

                        if ($lev == 0) {
                            $found[$key] = $word;
                            self::deleteValue($source, $key, $word, $mode);
                            $wc++;
                            break;
                        }

                        if ($lev < $distance || $distance == -1) {
                            $f_wip = $word;
                            $f_key = $key;
                            $distance = $lev;
                        }
                    }

                    $found[$f_key] = $f_wip;
                    self::deleteValue($source, $f_key, $f_wip, $mode);
                    $wc++;
                    $distance = -1;
                }
            } else
                break;
        }

        if ($count == 1) {
            reset($found);
            $fk = key($found);
            return $found[$fk];
        } else
            return $found;
    }

    private static function deleteValue(&$array, $key, $value, $mode) {
        if ($mode == 1) {
            $fnd = true;
            while ($fnd) {
                $fnd = false;
                $key = array_search($value, $array);
                if (gettype($key) != "boolean") {
                    unset($array[$key]);
                    $fnd = true;
                }
            }
        } else
            unset($array[$key]);
    }

}
