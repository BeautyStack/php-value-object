<?php

namespace Beautystack\Value\Implementation\Money;

use Beautystack\Value\Contracts\ValueObjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Money implements \Beautystack\Value\Contracts\Money\Money
{
    private int $amount;
    private \Beautystack\Value\Contracts\Money\Currency $currency;

    private function __construct(int $amount, \Beautystack\Value\Contracts\Money\Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function fromInt(int $amount, \Beautystack\Value\Contracts\Money\Currency $currency): Money
    {
        return new Money($amount, $currency);
    }

    public static function fromStrings(string $amount, string $currency): Money
    {
        $currencies = new \Money\Currencies\ISOCurrencies();
        $moneyParser = new \Money\Parser\DecimalMoneyParser($currencies);
        $money = $moneyParser->parse($amount, new \Money\Currency($currency));

        return new self((int) $money->getAmount(), Currency::fromString($money->getCurrency()->getCode()));
    }

    public function format(): string
    {
        $money = new \Money\Money($this->amount, new \Money\Currency($this->currency));
        $currencies = new \Money\Currencies\ISOCurrencies();
        $numberFormatter = new \NumberFormatter('en', \NumberFormatter::CURRENCY);
        $moneyFormatter = new \Money\Formatter\IntlMoneyFormatter($numberFormatter, $currencies);

        return $moneyFormatter->format($money);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): \Beautystack\Value\Contracts\Money\Currency
    {
        return $this->currency;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }


    public function subtract(\Beautystack\Value\Contracts\Money\Money $money): Money
    {
        $moneyPhp = new \Money\Money($this->getAmount(), new \Money\Currency($this->getCurrency()));
        $result = $moneyPhp->subtract(new \Money\Money($money->getAmount(), new \Money\Currency($money->getCurrency())));

        return Money::fromInt((int) $result->getAmount(), Currency::fromString($result->getCurrency()->getCode()));
    }

    public function add(\Beautystack\Value\Contracts\Money\Money $money): Money
    {
        $moneyPhp = new \Money\Money($this->getAmount(), new \Money\Currency($this->getCurrency()));
        $result = $moneyPhp->add(new \Money\Money($money->getAmount(), new \Money\Currency($money->getCurrency())));

        return Money::fromInt((int) $result->getAmount(), Currency::fromString($result->getCurrency()->getCode()));
    }

    public function percent(int ...$percentages): Collection
    {
        $moneyPhp = new \Money\Money($this->getAmount(), new \Money\Currency($this->getCurrency()));
        $newMoneyPhpArray = $moneyPhp->allocate($percentages);

        return new ArrayCollection(array_map(
            function (\Money\Money $moneyPhp) {
                return new Money((int) $moneyPhp->getAmount(), Currency::fromString($moneyPhp->getCurrency()->getCode()));
            },
            $newMoneyPhpArray
        ));
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency
        ];
    }

    public function isEqual(ValueObjectInterface $compareValueObject): bool
    {
        if (!$compareValueObject instanceof self::class) {
            return false;
        }
        return $this->jsonSerialize() === $compareValueObject->jsonSerialize();
    }
}