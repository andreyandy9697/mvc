<?php

namespace App\Cards;

use App\Cards\Cards;

class CardsHand
{
    private $hand = [];

    // public function add(Cards $card): void
    // {
    //     $this->hand[] = $card;
    // }

    // public function draw(): void
    // {
    //     foreach ($this->hand as $card) {
    //         $card->draw();
    //     }
    // }

    public function draw(int $count): void
    {
        $deckSize = count(CardsGraphic::DECK);
        if ($count < 1 || $count > $deckSize) {
            throw new \InvalidArgumentException("Dra mellan 1 och {$deckSize} kort.");
        }

        // shuffle a copy and take the top $count
        $deck = CardsGraphic::DECK;
        shuffle($deck);
        $this->hand = array_slice($deck, 0, $count);
    }

    // Antal kort i handen
    public function getNumberCards(): int
    {
        return count($this->hand);
    }

    // returnerar kortnummer (1-52) genom att omvandla arrayen Deck
    public function getValues(): array
    {
        $flip = array_flip(CardsGraphic::DECK);
        return array_map(fn($card) => $flip[$card] + 1, $this->hand);
    }

    // returnerar grafik (A♠)
    public function getString(): array
    {
        return $this->hand;
    }
}