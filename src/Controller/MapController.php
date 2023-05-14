<?php

namespace App\Controller;

use App\Entity\Barbershop;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(ManagerRegistry $doctrine): Response
    {   
        $barbershops = $doctrine->getRepository(Barbershop::Class)->findBy([], ["nom"=>"ASC"]);
        
        foreach ($barbershops as $barbershop) {
            $latitude = $barbershop->getLatitude();
            $longitude = $barbershop->getLongitude();
            
            // Ajoutez les coordonnées à un tableau
            $coordinates[] = [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        }

        return $this->render('map/index.html.twig', [
            'coordinates' => $coordinates
        ]);
    }
}
