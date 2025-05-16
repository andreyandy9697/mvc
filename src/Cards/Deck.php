<?php

namespace App\Cards;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Deck
{
    /** @var string[] */
    private array $cards;

    public function __construct()
    {
        // starta med en ny "deck"
        $this->cards = CardsGraphic::DECK;
    }

    // hämtar från sessionen, eller startar från 0
    public static function fromSession(SessionInterface $session): self
    {
        if ($session->has('card_deck')) {
            $deck = new self();
            $deck->cards = $session->get('card_deck');

            return $deck;
        }

        // skapar instans och sparar i session
        $deck = new self();
        $deck->saveToSession($session);

        return $deck;
    }

    // återskapar full "deck" och blandar kort.
    public function shuffle(): void
    {
        $this->cards = CardsGraphic::DECK;
        shuffle($this->cards);
    }

    // dra ut antal $count kort och returnerar korten och substraherar de från $this->cards.
    public function draw(int $count): array
    {
        return array_splice($this->cards, 0, $count);
    }

    // antalet kort som är kvar
    public function count(): int
    {
        return \count($this->cards);
    }

    // sparar kortleken i sessionen.
    public function saveToSession(SessionInterface $session): void
    {
        $session->set('card_deck', $this->cards);
    }
}
