<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class About extends AbstractController
{
    #[Route('/about', name: 'about')]
    public function index(): Response
    {
        return $this->render('about.html.twig');
    }
}
