<?php

namespace App\Controller;

use App\Entity\Barbershop;
use App\Repository\BarbershopRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{   
    // Home
    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Récuperer les 3 derniers barbershops de la BDD

        $lastBarbershops = $doctrine->getRepository(Barbershop::Class)->getLastThreeValidBarbershop();
        $lastArticles = $doctrine->getRepository(Barbershop::Class)->findBy([], [""=>"ASC"] , 3);

        return $this->render('home/index.html.twig', [
            'lastBarbershops' => $lastBarbershops
        ]);
    }
}
