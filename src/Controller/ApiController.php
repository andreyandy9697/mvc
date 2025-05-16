<?php

namespace App\Controller;

use App\Cards\CardsGraphic;
use App\Cards\Deck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    /**
     * HTML översikt av alla routes.
     */
    #[Route('', name: 'api_index', methods: ['GET'])]
    public function index(RouterInterface $router): Response
    {
        $routes = [];
        foreach ($router->getRouteCollection()->all() as $name => $route) {
            $routes[] = [
                'name' => $name,
                'path' => $route->getPath(),
            ];
        }

        usort($routes, static fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $this->render('api/index.html.twig', [
            'routes' => $routes,
        ]);
    }

    /**
     * GET /api/deck
     * returnerar hela kortleken som JSON.
     */
    #[Route('/deck', name: 'api_deck', methods: ['GET'])]
    public function deck(SessionInterface $session): JsonResponse
    {
        $session->start();
        if (!$session->has('card_deck')) {
            $session->set('card_deck', CardsGraphic::DECK);
        }

        return $this->json($session->get('card_deck'));
    }

    /**
     * POST /api/deck/shuffle
     * blandar om kortleken, sparar den i sessionen och returnerar den.
     */
    #[Route('/deck/shuffle', name: 'api_deck_shuffle', methods: ['POST'])]
    public function shuffle(SessionInterface $session): JsonResponse
    {
        $session->start();
        $deck = CardsGraphic::DECK;
        shuffle($deck);
        $session->set('card_deck', $deck);

        return $this->json($deck);
    }

    /**
     * POST /api/deck/draw
     * drar 1 kort, minskar sessionens kortlek, returnerar drawn & remaining.
     */
    #[Route('/deck/draw', name: 'api_deck_draw', methods: ['POST'])]
    public function drawOne(SessionInterface $session): JsonResponse
    {
        $session->start();
        $deck = $session->get('card_deck', CardsGraphic::DECK);
        if (empty($deck)) {
            return $this->json([
                'drawn' => [],
                'remaining' => 0,
            ]);
        }

        $card = array_shift($deck);
        $session->set('card_deck', $deck);

        return $this->json([
            'drawn' => [$card],
            'remaining' => \count($deck),
        ]);
    }

    /**
     * POST /api/deck/draw/{number}
     * drar ut antal {number} kort från deck, returnerar arrayen med drawn[] och det som är kvar.
     */
    #[Route('/deck/draw/{number<\d+>}', name: 'api_deck_draw_number', methods: ['POST'])]
    public function drawMultiple(int $number, SessionInterface $session): JsonResponse
    {
        $session->start();
        $deck = $session->get('card_deck', CardsGraphic::DECK);
        $drawn = [];

        for ($i = 0; $i < $number && !empty($deck); ++$i) {
            $drawn[] = array_shift($deck);
        }
        $session->set('card_deck', $deck);

        return $this->json([
            'drawn' => $drawn,
            'remaining' => \count($deck),
        ]);
    }

    /**
     * POST /api/deck/deal/{players}/{cards}
     * delar ut antal {cards} kort till antal valda {players} spelare, returnerar hands ochdet som är kvar.
     */
    #[Route('/deck/deal/{players<\d+>}/{cards<\d+>}', name: 'api_deck_deal', methods: ['POST'])]
    public function deal(int $players, int $cards, SessionInterface $session): JsonResponse
    {
        $session->start();
        // hämta eller initiera deck i session
        $deckModel = Deck::fromSession($session);

        $totalNeeded = $players * $cards;
        if ($deckModel->count() < $totalNeeded) {
            // för få kort kvar → återställ och blanda om
            $deckModel->shuffle();
        }

        $drawn = $deckModel->draw($totalNeeded);
        $deckModel->saveToSession($session);

        // dela kort till spelare
        $hands = [];
        for ($p = 1; $p <= $players; ++$p) {
            $start = ($p - 1) * $cards;
            $hands["Player {$p}"] = \array_slice($drawn, $start, $cards);
        }

        return $this->json([
            'hands' => $hands,
            'remaining' => $deckModel->count(),
        ]);
    }
}
