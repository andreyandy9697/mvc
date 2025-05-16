<?php

namespace App\Cards;

use App\Cards\Cards;

class CardsHand
{
    private $hand = [];

     public function draw(int $count): void
    {
        $deckSize = count(CardsGraphic::DECK);
        if ($count < 1 || $count > $deckSize) {
            throw new \InvalidArgumentException("Dra mellan 1 och {$deckSize} kort.");
        }

    // blanda
        $deck = CardsGraphic::DECK;
        shuffle($deck);
        $this->hand = array_slice($deck, 0, $count);
    }

    // kort
    public function getNumberCards(): int
    {
        return count($this->hand);
    }

    // returnerar kortnummer
    public function getValues(): array
    {
        $flip = array_flip(CardsGraphic::DECK);
        return array_map(fn($card) => $flip[$card] + 1, $this->hand);
    }

    // grafik (A♠)
    public function getString(): array
    {
        return $this->hand;
    }
}