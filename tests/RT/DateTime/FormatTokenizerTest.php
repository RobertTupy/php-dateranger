<?php

namespace RT\DateTime;

use PHPUnit\Framework\TestCase;

class FormatTokenizerTest extends TestCase
{
    /**
     * @param $format
     * @param $requiredTokens
     * @dataProvider parsingFormatProvider
     */
    public function testFormatParsing($format, $requiredTokens)
    {
        $tokenizer = new FormatTokenizer($format);

        $this->assertEquals($requiredTokens, $tokenizer->getTokens());
    }

    public function parsingFormatProvider()
    {
        return [
            'empty format' => [ '', [] ],
            'europe format' => [ 'Y-m-d',
                [
                    new FormatToken('Y', 0, true, FormatTokenizer::createDateTimeToken('Y')),
                    new FormatToken('-', 1, true, null),
                    new FormatToken('m', 2, true, FormatTokenizer::createDateTimeToken('n')),
                    new FormatToken('-', 3, true, null),
                    new FormatToken('d', 4, true, FormatTokenizer::createDateTimeToken('j')),
                ]
            ],

            'europe with modifier' => [ '?Y-m-d',
                [
                    new FormatToken('Y', 0, false, FormatTokenizer::createDateTimeToken('Y')),
                    new FormatToken('-', 1, false, null),
                    new FormatToken('m', 2, true, FormatTokenizer::createDateTimeToken('n')),
                    new FormatToken('-', 3, true, null),
                    new FormatToken('d', 4, true, FormatTokenizer::createDateTimeToken('j')),
                ]
            ],

            'non format chars are required by default' => [ '-Y-m',
                [
                    new FormatToken('-', 0, true, null),
                    new FormatToken('Y', 1, true, FormatTokenizer::createDateTimeToken('Y')),
                    new FormatToken('-', 2, true, null),
                    new FormatToken('m', 3, true, FormatTokenizer::createDateTimeToken('n')),
                ]
            ],

            'modifier modifies also non format chars' => [ '?-Y-m',
                [
                    new FormatToken('-', 0, false, null),
                    new FormatToken('Y', 1, false, FormatTokenizer::createDateTimeToken('Y')),
                    new FormatToken('-', 2, false, null),
                    new FormatToken('m', 3, true, FormatTokenizer::createDateTimeToken('n')),
                ]
            ]
        ];
    }
}