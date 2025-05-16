<?php

namespace App\Cards;

class Cards
{
    protected ?int $value;

    public function __construct()
    {
        $this->value = null;
    }

    public function draw(): int
    {
        $this->value = random_int(1, 52);
        return $this->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getAsString(): string
    {
        return (string)$this->value;
    }
}
