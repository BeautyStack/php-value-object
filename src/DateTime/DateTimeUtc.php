<?php

namespace Beautystack\Value\Implementation\DateTime;

use Beautystack\Value\Contracts\DateTime\DateTime;
use Beautystack\Value\Contracts\DateTime\Timezone;
use Beautystack\Value\Contracts\ValueObjectInterface;
use DateTimeImmutable;

class DateTimeUtc implements DateTime
{
    public const TIME_ZONE_UTC = 'UTC';

    public const INTERVAL_ONE_DAY = 86400;

    public const INTERVAL_ONE_WEEK = 604800;

    public const INTERVAL_THREE_MONTHS = 7884000;

    public const INTERVAL_ONE_YEAR = 31536000;

    public const INTERVAL_FIFTEEN_MINUTES = 900;

    public const INTERVAL_TWO_HOURS = 7200;

    public const INTERVAL_FIVE_MINUTES = 300;

    private DateTimeImmutable $dateTimeImmutable;

    private function __construct(DateTimeImmutable $dateTimeImmutable)
    {
        $this->dateTimeImmutable = $dateTimeImmutable;
    }

    public static function fromString(string $time, ?Timezone $timezone = null): DateTime
    {
        $timezone = isset($timezone) ? new \DateTimeZone($timezone->getValue()) : null;
        $dateTimeImmutable = (new DateTimeImmutable($time, $timezone))
            ->setTimezone(new \DateTimeZone(static::TIME_ZONE_UTC));

        return new DateTimeUtc($dateTimeImmutable);
    }

    public static function fromInterval(int $seconds, DateTimeUtc $dateTimeFrom = null): DateTime
    {
        if (empty($dateTimeFrom)) {
            $dateTimeFrom = DateTimeUtc::fromNow();
        }
        return new self(
            $dateTimeFrom->dateTimeImmutable->add(
                new \DateInterval(sprintf('PT%dS', $seconds))
            )
        );
    }

    public static function fromPhpDateTime(\DateTime $date): DateTime
    {
        return DateTimeUtc::fromString($date->format('c'));
    }

    public static function fromNow(): DateTime
    {
        return DateTimeUtc::fromString('now');
    }

    public static function fromUnixEpoch(): DateTime
    {
        return static::fromTimestamp(0);
    }

    public static function fromTimestamp(int $timestamp): DateTime
    {
        return DateTimeUtc::fromString((new \DateTime(sprintf('@%d', $timestamp)))->format('c'));
    }

    public static function fromTimestampMs(int $milliseconds): DateTime
    {
        $timestamp = $milliseconds > 0 ? intval($milliseconds / 1000) : 0;
        return self::fromTimestamp($timestamp);
    }

    public function addInterval(int $seconds): DateTimeUtc
    {
        return DateTimeUtc::fromInterval($seconds, $this);
    }

    public function removeInterval(int $seconds): DateTime
    {
        if (empty($dateTimeFrom)) {
            $dateTimeFrom = DateTimeUtc::fromNow();
        }
        return new self(
            $dateTimeFrom->dateTimeImmutable->sub(
                new \DateInterval(sprintf('PT%dS', $seconds))
            )
        );
    }

    public function firstDayOfLastMonth(Timezone $timezone, string $time = '00:00:00'): DateTime
    {
        return DateTimeUtc::fromString(
            $this->toPhpDateTime()
                ->setTimezone(new \DateTimeZone($timezone->getValue()))
                ->modify('first day of last month')
                ->format(sprintf('Y-m-d %s', $time)),
            $timezone
        );
    }

    public function firstDayOfNextMonth(Timezone $timezone, string $time = '00:00:00'): DateTime
    {
        return DateTimeUtc::fromString(
            $this->toPhpDateTime()
                ->setTimezone(new \DateTimeZone($timezone->getValue()))
                ->modify('first day of next month')
                ->format(sprintf('Y-m-d %s', $time)),
            $timezone
        );
    }

    public function lastDayOfThisMonth(Timezone $timezone, string $time = '00:00:00'): DateTime
    {
        return DateTimeUtc::fromString(
            $this->toPhpDateTime()
                ->setTimezone(new \DateTimeZone($timezone->getValue()))
                ->modify('last day of this month')
                ->format(sprintf('Y-m-d %s', $time)),
            $timezone
        );
    }

    public function firstDayOfThisMonth(Timezone $timezone, string $time = '00:00:00'): DateTime
    {
        return DateTimeUtc::fromString(
            $this->toPhpDateTime()
                ->setTimezone(new \DateTimeZone($timezone->getValue()))
                ->modify('first day of this month')
                ->format(sprintf('Y-m-d %s', $time)),
            $timezone
        );
    }

    public function format(string $format, Timezone $timezone): string
    {
        return $this->dateTimeImmutable->setTimezone(new \DateTimeZone($timezone))->format($format);
    }

    public function toPhpDateTime(): \DateTime
    {
        return new \DateTime($this->getValue());
    }

    public function getValue(): string
    {
        return $this->dateTimeImmutable->format('c');
    }

    public function getTimestamp() : int
    {
        return $this->dateTimeImmutable->getTimestamp();
    }

    public function isBefore(DateTime $compareDate, bool $inclusive = false): bool
    {
        if ($inclusive) {
            return $this->getTimestamp() <= $compareDate->getTimestamp();
        }

        return $this->getTimestamp() < $compareDate->getTimestamp();
    }

    public function isAfter(DateTime $compareDate, bool $inclusive = false): bool
    {
        if ($inclusive) {
            return $this->getTimestamp() >= $compareDate->getTimestamp();
        }

        return $this->getTimestamp() > $compareDate->getTimestamp();
    }

    public function jsonSerialize(): string
    {
        return $this->getValue();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function isEqual(ValueObjectInterface $compareValueObject): bool
    {
        if (!$compareValueObject instanceof self::class) {
            return false;
        }
        return $this->jsonSerialize() === $compareValueObject->jsonSerialize();
    }
}