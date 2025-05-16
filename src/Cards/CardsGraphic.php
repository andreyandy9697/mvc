<?php

namespace App\Cards;

class CardsGraphic extends Cards
{

    public const DECK = [
        // Spades
        'A♠','2♠','3♠','4♠','5♠','6♠','7♠','8♠','9♠','10♠','J♠','Q♠','K♠',
        // Hearts
        'A♥','2♥','3♥','4♥','5♥','6♥','7♥','8♥','9♥','10♥','J♥','Q♥','K♥',
        // Diamonds
        'A♦','2♦','3♦','4♦','5♦','6♦','7♦','8♦','9♦','10♦','J♦','Q♦','K♦',
        // Clubs
        'A♣','2♣','3♣','4♣','5♣','6♣','7♣','8♣','9♣','10♣','J♣','Q♣','K♣',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returnerar unicode istället för “[n]”.
     */
    public function getAsString(): string
    {
        // value is 1–52, so subtract 1 for zero index
        return self::DECK[$this->getValue() - 1];
    }
}
