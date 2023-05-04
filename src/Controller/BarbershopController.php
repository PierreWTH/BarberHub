<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BarbershopController extends AbstractController
{
    #[Route('/barbershop', name: 'app_barbershop')]
    public function index(): Response
    {
        return $this->render('barbershop/index.html.twig', [
            'controller_name' => 'BarbershopController',
        ]);
    }
}
