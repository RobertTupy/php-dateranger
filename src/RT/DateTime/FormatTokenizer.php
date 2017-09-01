<?php

namespace RT\DateTime;


class FormatTokenizer
{
    private $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    /**
     * @return array|FormatToken[]
     */
    public function getTokens()
    {
        $tokens = [];

        // token => datetime formatter
        $dateTimeTokens = [
            // year
            'Y' => 'Y',
            'y' => 'Y',
            // month
            'F' => 'n',
            'm' => 'n',
            'M' => 'n',
            'n' => 'n',
            // day
            'd' => 'j',
            'D' => 'j',
            'j' => 'j',
            'l' => 'j',
            // hour
            'g' => 'G',
            'G' => 'G',
            'h' => 'G',
            'H' => 'G',
            // minutes
            'i' => 'i',
            // seconds
            's' => 's',
            // miliseconds
            'v' => 'v',
            // microseconds
            'u' => 'u',
        ];

        $modifiers = [
            '?',
        ];

        $chunks = strlen($this->format) > 0 ? str_split($this->format) : [];
        $position = 0;
        $nextRequired = true;
        $nextNonDtRequired = true;
        foreach ($chunks as $char) {
            $dateTimeToken = isset($dateTimeTokens[$char]) ? self::createDateTimeToken($dateTimeTokens[$char]) : null;
            if (!in_array($char, $modifiers)) {
                $required = true;
                if ($dateTimeToken) {
                    if (!$nextRequired) {
                        $required = false;
                        $nextRequired = true;
                        $nextNonDtRequired = false;
                    } else {
                        $nextNonDtRequired = true;
                    }
                } else {
                    $required = $nextNonDtRequired;
                }
                $tokens[] = new FormatToken($char, $position++, $required, $dateTimeToken);

            } else {
                if ($char === '?') {
                    $nextRequired = false;
                    $nextNonDtRequired = false;
                }
            }
        }
        return $tokens;
    }

    public static function createDateTimeToken($formatter)
    {
        $levelMap = [
            // year
            'Y' => 1,
            // month
            'n' => 2,
            // day
            'j' => 3,
            // hour
            'G' => 4,
            // minutes
            'i' => 5,
            // seconds
            's' => 6,
            // miliseconds
            'v' => 7,
            // microseconds
            'u' => 8,
        ];
        if (!array_key_exists($formatter, $levelMap)) {
            throw new \InvalidArgumentException('unknown formatter passed: ' . $formatter);
        }

        return new DateTimeToken($formatter, $levelMap[$formatter]);
    }
}