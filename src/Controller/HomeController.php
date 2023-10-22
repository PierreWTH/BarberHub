<?php

namespace App\Controller;

use App\Entity\Article;
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
        //Récuperer les 2 derniers articles 
        $lastArticles = $doctrine->getRepository(Article::Class)->findBy([], ["date"=>"DESC"] , 2);
        $isHomePage = true;
        return $this->render('home/index.html.twig', [
            'lastBarbershops' => $lastBarbershops,
            'lastArticles' => $lastArticles,
            'isHomePage' => $isHomePage
        ]);
    }

    #[Route('/privacy', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('home/privacy.html.twig');
    }
}
