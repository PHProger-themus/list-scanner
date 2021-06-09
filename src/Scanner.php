<?php

namespace phproger\scanner;

use View;

class Scanner implements ScannerInterface
{
    public static function find(string $input, $source, int $count = 1, int $looking_from = self::ARRAY_BEGIN, int $mode = self::SEARCH_BY_VALUES, string $delimiter = ' ')
    {

        $found = [];
        $wc = 0;

        if (gettype($source) == "string") {
            $source = str_replace('-', ' ', trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $source)));
            $source = explode(' ', $source);
        }

        $input = explode($delimiter, $input);
        $input = array_unique($input);
        $source = array_unique($source);

        switch ($looking_from) {
            case self::ARRAY_END:
                $input = array_reverse($input);
                break;
            case self::ARRAY_RAND:
                shuffle($input);
                break;
        }

        $last_word = end($input);

        if ($count > count($source)) {
            View::setError('Невозможно получить больше слов, чем содержит источник');
        }

        //Поиск точных вхождений.

        foreach ($input as $keyword) {
            if ($wc < $count) {
                foreach ($source as $key => $word) {

                    $pos = ($mode == 1) ? strripos($word, $keyword) : strripos($key, $keyword);

                    if ($pos !== false) {
                        $found[$key] = $word;
						unset($source[$key]);
                        $wc++;
                    }

                }
            }
        }

        //После того, как все точные вхождения найдены, поищем алгоритмом Левенштейна.

        foreach ($input as $keyword) {
            if ($wc < $count) {

                if ($keyword == $last_word) {
                    $final_wc = $count;
                }

                do {

                    $distance = -1;
                    $f_wip = $f_key = null;

                    foreach ($source as $key => $word) {
                        $lev = ($mode == 1) ? levenshtein($keyword, $word) : levenshtein($keyword, $key);

                        if ($lev < $distance || $distance == -1) {
                            $f_wip = $word;
                            $f_key = $key;
                            $distance = $lev;
                        }
                    }

                    $found[$f_key] = $f_wip;
					unset($source[$f_key]);
                    $wc++;

                } while (isset($final_wc) && $wc < $final_wc);

            }
        }

        if ($count == 1) {
            return array_shift($found);
        }
        return $found;
    }
	
}
