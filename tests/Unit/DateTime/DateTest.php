<?php

declare(strict_types=1);

namespace App\Tests\Unit\DateTime;

use Beautystack\Value\Implementation\DateTime\Date;
use Beautystack\Value\Implementation\DateTime\DateTimeUtc;
use Beautystack\Value\Implementation\DateTime\Timezone;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function testFromDateTimeUtcSeptember(): void
    {
        $dateimeUtc = DateTimeUtc::fromString('2022-10-03 15:49:18', Timezone::fromString('Europe/London'));
        $dateUK = Date::fromDateTimeUtc($dateimeUtc, Timezone::fromString('Europe/London'));
        $dateAus = Date::fromDateTimeUtc($dateimeUtc, Timezone::fromString('Australia/Melbourne'));
        self::assertEquals('2022-10-03', $dateUK->getValue());
        self::assertEquals('2022-10-04', $dateAus->getValue());
    }

    public function testFromPhpDateTimeSeptember(): void
    {
        $dateimeUk = \DateTime::createFromFormat('Y-m-d H:i:s', '2022-10-03 15:49:18', new \DateTimezone('Europe/London'));
        $dateimeAus = (\DateTime::createFromFormat('Y-m-d H:i:s', '2022-10-03 15:49:18', new \DateTimezone('Europe/London')))->setTimezone(new \DateTimezone('Australia/Melbourne'));
        $dateUK = Date::fromPhpDateTime($dateimeUk);
        $dateAus = Date::fromPhpDateTime($dateimeAus);
        self::assertEquals('2022-10-03', $dateUK->getValue());
        self::assertEquals('2022-10-04', $dateAus->getValue());
    }

    public function testFromNow(): void
    {
        $dateUK = Date::fromNow(Timezone::fromString('Europe/London'));
        $phpDateTime = new \DateTime('now', new \DateTimeZone('Europe/London'));
        self::assertEquals($phpDateTime->format('Y-m-d'), $dateUK->getValue());
    }

    public function testItSerializesCorrectly(): void
    {
        $dateimeUtc = Date::fromDateTimeUtc(DateTimeUtc::fromString('2022-10-03 15:49:18', Timezone::fromString('Europe/London')), Timezone::fromString('Europe/London'));
        self::assertEquals('"2022-10-03"', json_encode($dateimeUtc));
    }

    public function testItConvertsToAString(): void
    {
        $dateimeUtc = Date::fromDateTimeUtc(DateTimeUtc::fromString('2022-10-03 15:49:18', Timezone::fromString('Europe/London')), Timezone::fromString('Europe/London'));
        self::assertEquals('2022-10-03', (string) $dateimeUtc);
    }
}
