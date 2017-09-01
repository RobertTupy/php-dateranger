<?php

namespace RT\DateTime;


use PHPUnit\Framework\TestCase;

class DateTimeRangerTest extends TestCase
{

    /**
     * @group single
     */
    public function testOne()
    {
        /*
        $ranger = new DateTimeRanger( new \DateTime('2017-01-01 10:00:00'), new \DateTime('2017-01-01 12:00:00'), 'H:i Y-m-d');
        $this->assertSame('10:00 - 12:00 2017-01-01', (string) $ranger);
        //*/
        $ranger = new DateTimeRanger( new \DateTime('2017-01-01 10:00:00'), new \DateTime('2017-02-01 12:00:00'), 'Y j.n. Y');
        $this->assertSame('1.1. - 1.2. 2017', (string) $ranger);
    }


    public function testDefaultFormat()
    {
        $ranger = new DateTimeRanger(new \DateTime('2017-01-01'), new \DateTime('2017-01-02'));

        $this->assertEquals('j.n. Y', $ranger->getFormat());
    }

    public function testCanDefineFormatInConstructor()
    {
        $format = \DateTime::ISO8601;
        $ranger = new DateTimeRanger(new \DateTime('2017-01-01'), new \DateTime('2017-01-02'), $format);

        $this->assertEquals($format, $ranger->getFormat());
    }

    public function testDefaultDelimiter()
    {
        $ranger = new DateTimeRanger(new \DateTime('2017-01-01'), new \DateTime('2017-01-02'));

        $this->assertEquals(' - ', $ranger->getDelimiter());
    }

    public function testCanDefineDelimiterInConstructor()
    {
        $delimiter = "&";
        $ranger = new DateTimeRanger(new \DateTime('2017-01-01'), new \DateTime('2017-01-02'), null, $delimiter);

        $this->assertEquals($delimiter, $ranger->getDelimiter());
    }

    public function testImplementsToStringMethod()
    {
        $ranger = new DateTimeRanger(new \DateTime('2017-01-01'), new \DateTime('2017-01-02'));

        $this->assertTrue(is_string((string) $ranger));
    }

    /**
     * @param $expectation
     * @param $start
     * @param $stop
     * @param $format
     * @param $delimiter
     * @dataProvider rangerTestProvider
     */
    public function testRanger($expectation, $start, $stop, $format = null, $delimiter = null)
    {
        $ranger = new DateTimeRanger($start, $stop, $format, $delimiter);

        $this->assertSame($expectation, (string) $ranger);
    }

    public function rangerTestProvider()
    {
        return [
            'empty format, default delimiter' => [ ' - ', new \DateTime('2017-01-01'), new \DateTime('2017-01-02'), ''],
            'empty format, empty delimiter' => [ '', new \DateTime('2017-01-01'), new \DateTime('2017-01-02'), '', ''],

            '1' => [ '1 - 2.1. 2017', new \DateTime('2017-01-01'), new \DateTime('2017-01-02')],
            '2' => [ '1.1 - 1.2. 2017', new \DateTime('2017-01-01'), new \DateTime('2017-02-01')],

            '3' => [ '2017-01-01 10:00 - 12:00', new \DateTime('2017-01-01 10:00:00'), new \DateTime('2017-01-01 12:00:00'), 'Y-m-d H:i'],

            '4' => [ '10:00 - 12:00 2017-01-01', new \DateTime('2017-01-01 10:00:00'), new \DateTime('2017-01-01 12:00:00'), 'H:i Y-m-d'],
        ];
    }

}