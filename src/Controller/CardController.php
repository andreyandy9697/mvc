<?php

namespace App\Controller;

use App\Cards\CardsGraphic;
use App\Cards\Deck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    // Landningssida för kortspelet

    #[Route('/card', name: 'card_index')]
    public function index(): Response
    {
        return $this->render('card/index.html.twig');
    }

    // visa ordnad kortlek (annars initierar en)

    #[Route('/card/deck', name: 'card_deck')]
    public function deck(SessionInterface $session): Response
    {
        $session->start();
        if (!$session->has('card_deck')) {
            $session->set('card_deck', CardsGraphic::DECK);
        }

        return $this->render('card/deck.html.twig', [
            'deck' => CardsGraphic::DECK,
        ]);
    }

    // blanda om hela kortleken (återställ + shuffle)

    #[Route('/card/deck/shuffle', name: 'card_deck_shuffle')]
    public function shuffleDeck(SessionInterface $session): Response
    {
        $session->start();
        $shuffled = CardsGraphic::DECK;
        shuffle($shuffled);
        $session->set('card_deck', $shuffled);

        return $this->render('card/deck_shuffle.html.twig', [
            'deck' => $shuffled,
        ]);
    }

    // dra ett kort från sessionens deck, spara kvarvarande

    #[Route('/card/deck/draw', name: 'card_deck_draw')]
    public function drawOne(SessionInterface $session): Response
    {
        $session->start();
        if (!$session->has('card_deck')) {
            $session->set('card_deck', CardsGraphic::DECK);
        }

        $deck = $session->get('card_deck');
        if (empty($deck)) {
            $this->addFlash('warning', 'Kortleken är tom - blanda om för att återställa.');

            return $this->redirectToRoute('card_deck_shuffle');
        }

        $card = array_shift($deck);
        $session->set('card_deck', $deck);

        return $this->render('card/deck_draw.html.twig', [
            'cards' => [$card],
            'remaining' => \count($deck),
        ]);
    }

    // dra ett antal (:number) kort på en gång
    #[Route('/card/deck/draw/{number<\d+>}', name: 'card_deck_draw_number')]
    public function drawMultiple(int $number, SessionInterface $session): Response
    {
        $session->start();
        if (!$session->has('card_deck')) {
            $session->set('card_deck', CardsGraphic::DECK);
        }

        $deck = $session->get('card_deck');
        $drawn = [];

        for ($i = 0; $i < $number && !empty($deck); ++$i) {
            $drawn[] = array_shift($deck);
        }

        $session->set('card_deck', $deck);

        return $this->render('card/deck_draw.html.twig', [
            'cards' => $drawn,
            'remaining' => \count($deck),
        ]);
    }

    /**
     * /card/deck/deal/{players}/{cards}
     * dela ut ett antal (:cards) kort till ett antal (:players) spelare, spara resten i sessionen.
     */
    #[Route('/card/deck/deal/{players<\d+>}/{cards<\d+>}', name: 'card_deck_deal')]
    public function deal(int $players, int $cards, SessionInterface $session): Response
    {
        $session->start();

        // återställ eller hämta befintlig spel
        $deck = Deck::fromSession($session);

        // återställ antalet kort
        $totalNeeded = $players * $cards;
        if ($deck->count() < $totalNeeded) {
            $this->addFlash('warning', 'Inte tillräckligt många kort kvar. Återställer och blandar om.');
            $deck->shuffle();
        }

        // dra alla på en gång, spara sedan kvar i session:
        $drawn = $deck->draw($totalNeeded);
        $deck->saveToSession($session);

        // fördela kort per spelare
        $hands = [];
        for ($p = 1; $p <= $players; ++$p) {
            $start = ($p - 1) * $cards;
            $hands["Spelare {$p}"] = \array_slice($drawn, $start, $cards);
        }

        return $this->render('card/deck_deal.html.twig', [
            'hands' => $hands,
            'remaining' => $deck->count(),
        ]);
    }
}
