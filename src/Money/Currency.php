<?php

namespace Beautystack\Value\Implementation\Money;

use Beautystack\Value\Contracts\Money\Exception\InvalidCurrencyException;
use Beautystack\Value\Contracts\ValueObjectInterface;
use Money\Currencies\ISOCurrencies;

class Currency implements \Beautystack\Value\Contracts\Money\Currency
{
    private string $value;

    private function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public static function fromString(string $value) : self
    {
        $value = strtoupper($value);
        if (empty($value)) {
            throw new InvalidCurrencyException('currency not set');
        }

        $currencies = new ISOCurrencies();
        if (! $currencies->contains(new \Money\Currency($value))) {
            throw new InvalidCurrencyException(sprintf('currency %s is not supported', $value));
        }
        return new self(
            $value
        );
    }

    public function jsonSerialize(): string
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

    public function getValue() : string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}