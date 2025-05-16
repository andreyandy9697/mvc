<?php

namespace App\Cards;

class CardsHand
{
    private $hand = [];

    public function draw(int $count): void
    {
        $deckSize = \count(CardsGraphic::DECK);
        if ($count < 1 || $count > $deckSize) {
            throw new \InvalidArgumentException("Dra mellan 1 och {$deckSize} kort.");
        }

        // gör en kopia, blanda kort och dra ut $count
        $deck = CardsGraphic::DECK;
        shuffle($deck);
        $this->hand = \array_slice($deck, 0, $count);
    }

    // antal kort i handen
    public function getNumberCards(): int
    {
        return \count($this->hand);
    }

    // returnerar kortnummer (1-52) genom att omvandla arrayen Deck
    public function getValues(): array
    {
        $flip = array_flip(CardsGraphic::DECK);

        return array_map(static fn ($card) => $flip[$card] + 1, $this->hand);
    }

    // returnerar grafik (A♠)
    public function getString(): array
    {
        return $this->hand;
    }
}
