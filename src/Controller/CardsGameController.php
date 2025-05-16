<?php

namespace App\Controller;

use App\Cards\Cards;
use App\Cards\CardsGraphic;
use App\Cards\CardsHand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardsGameController extends AbstractController
{
    #[Route("/game/kortspel", name: "kortspel_start")]
    public function home(): Response
    {
        return $this->render('kortspel/home.html.twig');
    }

    // testar att dra ett kort
    #[Route("/game/kortspel/test/draw", name: "test_draw_card")]
    public function testDrawCard(): Response
    {
        $card = new CardsGraphic();
        $card->draw();
        return $this->render('kortspel/test/draw.html.twig', [
            'cardIndex'  => $card->getValue(),
            'cardString' => $card->getAsString(),
        ]);
    }

    #[Route("/game/kortspel/test/draw/{num<\d+>}", name: "test_draw_num_cards")]
    public function testDrawCards(int $num): Response
    {
        if ($num > 52) {
            throw new \Exception("Kan inte dra mer än 52 kort!");
        }

        $cards = [];
        for ($i = 0; $i < $num; $i++) {
            $c = new CardsGraphic();
            $c->draw();
            $cards[] = $c->getAsString();
        }

        return $this->render('kortspel/test/draw_many.html.twig', [
            'num_cards' => count($cards),
            'cardsDraw' => $cards,
        ]);
    }

    #[Route("/game/kortspel/test/cardshand/{num<\d+>}", name: "test_cardshand")]
    public function testCardsHand(int $num): Response
    {
        $hand = new CardsHand();
        $hand->draw($num);

        return $this->render('kortspel/test/cardshand.html.twig', [
            'num_cards' => $hand->getNumberCards(),
            'cardsDraw' => $hand->getString(),
        ]);
    }

    // kortspel routes

    #[Route("/game/kortspel/init", name: "kortspel_init_get", methods: ['GET'])]
    public function init(): Response
    {
        return $this->render('kortspel/init.html.twig');
    }

    #[Route("/game/kortspel/init", name: "kortspel_init_post", methods: ['POST'])]
    public function initCallback(Request $request, SessionInterface $session): Response
    {
        $numCards = (int)$request->request->get('num_cards', 5);

        // skapa en hand, dra kort
        $hand = new CardsHand();
        $hand->draw($numCards);

        $session->set('kortspel_cardshand', $hand);
        $session->set('kortspel_cards', $numCards);
        $session->set('kortspel_round', 0);
        $session->set('kortspel_total', 0);

        return $this->redirectToRoute('kortspel_play');
    }

    #[Route("/game/kortspel/play", name: "kortspel_play", methods: ['GET'])]
    public function play(SessionInterface $session): Response
    {
        /** @var \App\Cards\CardsHand $hand */
        $hand = $session->get('kortspel_cardshand');

        // hämta grafik
        $cards = $hand->getString();

        // kortindex 1–52
        $flip = array_flip(\App\Cards\CardsGraphic::DECK);

        // skapa ett [ value, card ] par
        $cardsWithValues = array_map(
            fn(string $card): array => [
                'value' => $flip[$card] + 1,
                'card'  => $card,
            ],
            $cards
        );

        return $this->render('kortspel/play.html.twig', [
            'kortspelCards'  => $cardsWithValues,
            'kortspelRound'  => $session->get('kortspel_round', 0),
            'kortspelTotal'  => $session->get('kortspel_total', 0),
        ]);
    }


    #[Route("/game/kortspel/draw", name: "kortspel_draw", methods: ['POST'])]
    public function draw(SessionInterface $session): Response
    {
        /** @var CardsHand $hand */
        $hand = $session->get('kortspel_cardshand');

        // dra antalet från sessionen
        $numCards = $session->get('kortspel_cards', 5);
        $hand->draw($numCards);

        $round = 0;

        foreach ($hand->getValues() as $value) {
            if ($value === 1) {
                $this->addFlash('warning', 'Du drog en 1-a och förlorade allt!');
                $round = 0;
                break;
            }
            $round += $value;
        }

        $session->set('kortspel_round', $round);

        return $this->redirectToRoute('kortspel_play');
    }

    #[Route("/game/kortspel/save", name: "kortspel_save", methods: ['POST'])]
    public function save(SessionInterface $session): Response
    {
        $round = $session->get('kortspel_round', 0);
        $total = $session->get('kortspel_total', 0);

        $session->set('kortspel_total', $total + $round);
        $session->set('kortspel_round', 0);

        $this->addFlash('notice', 'Din hand har sparats!');

        return $this->redirectToRoute('kortspel_play');
    }
}
