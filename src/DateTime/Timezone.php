<?php

declare(strict_types=1);

namespace Beautystack\Value\Implementation\DateTime;

use Beautystack\Value\Contracts\DateTime\TimezoneInterface;
use Beautystack\Value\Contracts\ValueObjectInterface;

class Timezone implements TimezoneInterface
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function jsonSerialize(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(ValueObjectInterface $compareValueObject): bool
    {
        if (! $compareValueObject instanceof self) {
            return false;
        }
        return $this->jsonSerialize() === $compareValueObject->jsonSerialize();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
