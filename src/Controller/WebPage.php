<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Response;

class WebPage extends AbstractController
{
    // About Route
    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('WebPage/about.html.twig');
    }

    // Home Route
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('WebPage/home.html.twig');
    }

    // Report Route
    #[Route('/report', name: 'report')]
    public function report(): Response
    {
        return $this->render('WebPage/report.html.twig');
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

        return $this->render('WebPage/api.html.twig', [
            'routes' => $routeList,
        ]);
    }
}
