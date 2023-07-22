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
        $barbershops = $doctrine->getRepository(Barbershop::Class)->findAll();
        
        foreach ($barbershops as $barbershop) {
            $latitude = $barbershop->getLatitude();
            $longitude = $barbershop->getLongitude();
            $name = $barbershop->getNom();
            $adresse = $barbershop->getAdresse();
            $ville = $barbershop->getVille();
            
            // Ajoutez les coordonnées à un tableau
            $coordinates[] = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'name' => $name,
                // Encode url pour pas avoir d'erreur dans le JSON
                'adresse' => urlencode($adresse),
                'ville' => $ville
            ];
        }

        return $this->render('map/index.html.twig', [
            'coordinates' => $coordinates
        ]);
    }
}
