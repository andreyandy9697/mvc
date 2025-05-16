<?php

namespace App\Controller;

use App\Dice\Dice;
use App\Dice\DiceGraphic;
use App\Dice\DiceHand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DiceGameController extends AbstractController
{
    #[Route("/game/pig", name: "pig_start")]
    public function home(): Response
    {
        return $this->render('pig/home.html.twig');
    }

    #[Route("/game/pig/test/roll", name: "test_roll_dice")]
    public function testRollDice(): Response
    {
        $die = new Dice();

        return $this->render('pig/test/roll.html.twig', [
            "dice"       => $die->roll(),
            "diceString" => $die->getAsString(),
        ]);
    }

    #[Route("/game/pig/test/roll/{num<\d+>}", name: "test_roll_num_dices")]
    public function testRollDices(int $num): Response
    {
        if ($num > 99) {
            throw new \Exception("Can not roll more than 99 dices!");
        }

        $diceRoll = [];
        for ($i = 1; $i <= $num; $i++) {
            $die = new DiceGraphic();
            $die->roll();
            $diceRoll[] = $die->getAsString();
        }

        return $this->render('pig/test/roll_many.html.twig', [
            "num_dices" => count($diceRoll),
            "diceRoll"  => $diceRoll,
        ]);
    }

    #[Route("/game/pig/test/dicehand/{num<\d+>}", name: "test_dicehand")]
    public function testDiceHand(int $num): Response
    {
        if ($num > 99) {
            throw new \Exception("Can not roll more than 99 dices!");
        }

        $hand = new DiceHand();
        for ($i = 1; $i <= $num; $i++) {
            if ($i % 2 === 1) {
                $hand->add(new DiceGraphic());
            } else {
                $hand->add(new Dice());
            }
        }
        $hand->roll();

        return $this->render('pig/test/dicehand.html.twig', [
            "num_dices" => $hand->getNumberDices(),
            "diceRoll"  => $hand->getString(),
        ]);
    }

    #[Route("/game/pig/init", name: "pig_init_get", methods: ['GET'])]
    public function init(): Response
    {
        return $this->render('pig/init.html.twig');
    }

    #[Route("/game/pig/init", name: "pig_init_post", methods: ['POST'])]
    public function initCallback(
        Request $request,
        SessionInterface $session
    ): Response {
        $numDice = (int) $request->request->get('num_dices', 0);

        $hand = new DiceHand();
        for ($i = 1; $i <= $numDice; $i++) {
            $hand->add(new DiceGraphic());
        }
        $hand->roll();

        $session->set("pig_dicehand", $hand);
        $session->set("pig_dices", $numDice);
        $session->set("pig_round", 0);
        $session->set("pig_total", 0);

        return $this->redirectToRoute('pig_play');
    }

    #[Route("/game/pig/play", name: "pig_play", methods: ['GET'])]
    public function play(SessionInterface $session): Response
    {
        // Ensure the session has been initialized
        if (! $session->has('pig_dicehand')) {
            return $this->redirectToRoute('pig_init_get');
        }

        /** @var DiceHand $dicehand */
        $dicehand = $session->get("pig_dicehand");

        return $this->render('pig/play.html.twig', [
            "pigDices"   => $session->get("pig_dices", 0),
            "pigRound"   => $session->get("pig_round", 0),
            "pigTotal"   => $session->get("pig_total", 0),
            "diceValues" => $dicehand->getString(),
        ]);
    }

    #[Route("/game/pig/roll", name: "pig_roll", methods: ['POST'])]
    public function roll(SessionInterface $session): Response
    {
        /** @var DiceHand $hand */
        $hand = $session->get("pig_dicehand");
        $hand->roll();

        $roundTotal = (int) $session->get("pig_round", 0);
        $round      = 0;

        foreach ($hand->getValues() as $value) {
            if ($value === 1) {
                $this->addFlash('warning', 'You got a 1 and you lost the round points!');
                $round      = 0;
                $roundTotal = 0;
                break;
            }
            $round += $value;
        }

        $session->set("pig_round", $roundTotal + $round);

        return $this->redirectToRoute('pig_play');
    }

    #[Route("/game/pig/save", name: "pig_save", methods: ['POST'])]
    public function save(SessionInterface $session): Response
    {
        $roundTotal = (int) $session->get("pig_round", 0);
        $gameTotal  = (int) $session->get("pig_total", 0);

        $session->set("pig_total", $gameTotal + $roundTotal);
        $session->set("pig_round", 0);

        $this->addFlash('notice', 'Your round was saved to the total!');

        return $this->redirectToRoute('pig_play');
    }
}
