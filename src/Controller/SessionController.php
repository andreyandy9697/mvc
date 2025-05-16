<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'session_index')]
    public function index(SessionInterface $session): Response
    {
        // dumpa allt om sessionen för debugging
        $data = $session->all();

        return $this->render('session/index.html.twig', [
            'sessionData' => $data,
        ]);
    }

    // raderar session
    #[Route('/session/delete', name: 'session_delete')]
    public function delete(SessionInterface $session): Response
    {
        $session->clear();
        $this->addFlash('success', 'Nu är sessionen raderad.');

        return $this->redirectToRoute('session_index');
    }
}
