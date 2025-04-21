<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class Report extends AbstractController
{
    #[Route('/report', name: 'report')]
    public function index(): Response
    {
        return $this->render('report.html.twig');
    }
}
