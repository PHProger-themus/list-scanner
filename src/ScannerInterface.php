<?php

namespace phproger\scanner;

interface ScannerInterface
{
    /**
     * @var int Константа, говорящая функции find() о том, что нужно выполнить поиск по ключу.
    */
    public const SEARCH_BY_KEYS = 0;

    /**
     * @var int Константа, говорящая функции find() о том, что нужно выполнить поиск по значению.
    */
    public const SEARCH_BY_VALUES = 1;

    /**
     * @var int Константа, говорящая функции find() о том, что нужно начать поиск с начала массива.
    */
    public const ARRAY_BEGIN = 0;

    /**
     * @var int Константа, говорящая функции find() о том, что нужно начать поиск с конца массива.
    */
    public const ARRAY_END = 1;

    /**
     * @var int Константа, говорящая функции find() о том, что нужно отсортировать массив случайным образом и начать поиск.
    */
    public const ARRAY_RAND = 2;

    /**
     * Ищет слова в тексте / массиве с помощью поиска совпадений и алгоритма Левенштейна, если требуемое количество больше найденных слов.
     * @param string $input Строка (слово), которое требуется найти.
     * @param string|array $source Массив слов / текст, в котором требуется выполнить поиск.
     * @param int $count Количество слов, которое нужно получить на выходе.
     * @param int $mode Константа, говорящая о том, как производить поиск в массиве слов (текст тоже становится массивом) - искать в ключах массива, или же значениях.
     * @param string $delimiter Разделитель строки $input, по которому она будет разбиваться, и последующие фрагменты текста будут участвовать в поиске.
     * @param int $looking_from Как производить поиск.
     * @return int|string Вернет массив слов, либо строку, если параметр $count равен 1.
    */
    public static function find(string $input, $source, int $count = 1, int $looking_from = self::ARRAY_BEGIN, int $mode = self::SEARCH_BY_VALUES, string $delimiter = ' ');
}
