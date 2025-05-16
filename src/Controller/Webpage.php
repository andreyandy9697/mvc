<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class Webpage extends AbstractController
{
    // About Route
    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('Webpage/about.html.twig');
    }

    // Home Route
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('Webpage/home.html.twig');
    }

    // Report Route
    #[Route('/report', name: 'report')]
    public function report(): Response
    {
        return $this->render('Webpage/report.html.twig');
    }

    // API Route
    #[Route('/api', name: 'api')]
    public function api(RouterInterface $router): Response
    {
        $allRoutes = $router->getRouteCollection();
        $routeList = [];

        foreach ($allRoutes as $name => $route) {
            $path = $route->getPath();
            if (str_starts_with($path, '/')) {
                $routeList[] = [
                    'name' => $name,
                    'path' => $path,
                ];
            }
        }

        return $this->render('Webpage/api.html.twig', [
            'routes' => $routeList,
        ]);
    }
}
